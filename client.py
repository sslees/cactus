#! /usr/bin/python3

# File: client.py
# Author: Samuel Lees (sslees)
# Date: 3/8/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project
# References:
#  http://www.bogotobogo.com/python/python_network_programming_server_client.php
#  https://pymotw.com/2/socket/binary.html

import RPi.GPIO as GPIO
import socket
import sys
import time
import uuid

from constants import *
import measure
import notifications

UUID_LBL = 'uuid='

try: uuid = uuid.UUID(open(CONFIG_FILE).read()[len(UUID_LBL):-1])
except:
   uuid = uuid.uuid4()
   uuidFile = open(CONFIG_FILE, 'w')
   uuidFile.write(UUID_LBL + str(uuid) + '\n')
   uuidFile.close()
GPIO.setmode(GPIO.BOARD)
for pump_pin in PUMP_PINS: GPIO.setup(pump_pin, GPIO.OUT)
values = (uuid.bytes, LOG_IP, 0, 0)
data = PACKET_FORMAT.pack(*values)
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect((HOST, PORT))
s.sendall(data)
s.shutdown(socket.SHUT_RDWR)
s.close()
while True:
   measurements = [] * 8
   for channel in range(8):
      measurements[channel] = measure.measure(channel)
      values = (uuid.bytes, channel, int(time.time()), measurements[channel])
      data = PACKET_FORMAT.pack(*values)
      s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
      s.connect((HOST, PORT))
      s.sendall(data)
      s.shutdown(socket.SHUT_RDWR)
      s.close()
   for channel in range(8):
      if measurements[channel] > DRY_THRESHOLD: # drier -> higher value
         notifications.notify_dry()
         while measurements[channel] > WATERED_THRESHOLD:
            GPIO.output(PUMP_PINS[channel], GPIO.HIGH)
            time.sleep(1)
            GPIO.output(PUMP_PINS[channel], GPIO.LOW)
            time.sleep(1)
            measurements[channel] = measure.measure(channel)
         notifications.notify_watered()
   time.sleep(UPDATE_INTERVAL)
