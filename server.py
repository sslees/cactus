#! /usr/bin/python3

# File: server.py
# Author: Samuel Lees (sslees)
# Date: 3/31/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project
# References:
#  http://www.bogotobogo.com/python/python_network_programming_server_client.php
#  https://pymotw.com/2/socket/binary.html

import socketserver
import uuid

from constants import *
import notifications
import sql

class MyTCPHandler(socketserver.BaseRequestHandler):
   def handle(self):
      self.data = self.request.recv(PACKET_FORMAT.size)
      values = PACKET_FORMAT.unpack(self.data)
      device = str(uuid.UUID(bytes=values[0]))
      channel = values[1]
      ip = self.client_address[0]
      timestamp = values[2]
      value = values[3]
      print('device: ', device, ', channel: ', channel, ' (ip: ', ip,  ')\n'
         '   timestamp: ', timestamp, ', value: ', value, sep='')
      if channel == -1: sql.handle_device(device, ip)
      else: sql.handle_measurement(device, channel, timestamp, value)

sql.initialize_db()
socketserver.TCPServer.allow_reuse_address = True
server = socketserver.TCPServer(('', PORT), MyTCPHandler)
server.serve_forever()
