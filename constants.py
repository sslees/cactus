# File: constants.py
# Author: Samuel Lees (sslees)
# Date: 3/31/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project

import struct

UUID_FILE = 'cactus.config'
PACKET_FORMAT = struct.Struct('! 16s i L I')
HOST = 'cactus.sslees.com'
PORT = 49151
UPDATE_INTERVAL = 10 # seconds
LOG_IP = -1
DB_DATABASE = "cactus"
DB_USER = "cactus"
DB_PASSWORD = "c@c7u$"
# DRY_THRESHOLD = 717 # less than 30% moisture #### not used yet
# WATERED_THRESHOLD = 562 # more than 45%  moisture #### not used yet
