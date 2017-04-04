#! /usr/bin/python3

# File: client.py
# Author: Samuel Lees (sslees)
# Date: 3/8/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project
# References:
#  http://www.bogotobogo.com/python/python_network_programming_server_client.php
#  https://pymotw.com/2/socket/binary.html

import socket
import sys
import time
import uuid

from constants import *
import measure

UUID_LBL = 'uuid='

channel = int(sys.argv[1])
try: uuid = uuid.UUID(open(CONFIG_FILE).read()[len(UUID_LBL):-1])
except:
   uuid = uuid.uuid4()
   uuidFile = open(CONFIG_FILE, 'w')
   uuidFile.write(UUID_LBL + str(uuid) + '\n')
   uuidFile.close()
values = (uuid.bytes, LOG_IP, 0, 0)
data = PACKET_FORMAT.pack(*values)
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect((HOST, PORT))
s.sendall(data)
s.shutdown(socket.SHUT_RDWR)
s.close()
while True:
   values = (uuid.bytes, channel, int(time.time()), measure.measure(channel))
   data = PACKET_FORMAT.pack(*values)
   s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
   s.connect((HOST, PORT))
   s.sendall(data)
   s.shutdown(socket.SHUT_RDWR)
   s.close()
   time.sleep(UPDATE_INTERVAL)
