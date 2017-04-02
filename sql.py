# File: sql.py
# Author: Samuel Lees (sslees)
# Date: 3/31/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project

import mysql.connector
import time

from constants import *

def initialize_db():
   cnx = mysql.connector.connect(
      user=USER, password=PASSWORD, database=DATABASE)
   cursor = cnx.cursor()
   cursor.execute(
      "CREATE TABLE IF NOT EXISTS users (" \
      "   email VARCHAR(254) PRIMARY KEY," \
      "   first_name VARCHAR(64) NOT NULL," \
      "   last_name VARCHAR(64) NOT NULL," \
      "   password_hash VARCHAR(255) NOT NULL);")
   cursor.execute(
      "CREATE TABLE IF NOT EXISTS devices (" \
      "   uuid CHAR(36) PRIMARY KEY," \
      "   ip VARCHAR(39)," \
      "   user VARCHAR(254)," \
      "   nickname VARCHAR(64)," \
      "   FOREIGN KEY (user) REFERENCES users (email));")
   cursor.execute(
      "CREATE TABLE IF NOT EXISTS measurements (" \
      "   id INTEGER AUTO_INCREMENT PRIMARY KEY," \
      "   device CHAR(36) NOT NULL," \
      "   channel INTEGER NOT NULL," \
      "   timestamp DATETIME NOT NULL," \
      "   value INTEGER UNSIGNED NOT NULL," \
      "   FOREIGN KEY (device) REFERENCES devices (uuid));")
   cnx.close()

def handle_device(uuid, ip):
   cnx = mysql.connector.connect(
      user=USER, password=PASSWORD, database=DATABASE)
   cursor = cnx.cursor()
   cursor.execute('INSERT INTO devices (uuid, ip) VALUES (%s %s);', (uuid, ip))
   cnx.commit()
   cnx.close()

def handle_measurement(device, channel, timestamp, value):
   cnx = mysql.connector.connect(
      user=DB_USER, password=DB_PASSWORD, database=DB_DATABASE)
   cursor = cnx.cursor()
   timeStr = time.strftime('%Y-%m-%d %H:%M:%S', time.gmtime(timestamp))
   cursor.execute('INSERT INTO measurements VALUES (0, %s, %s, %s, %s);',
      (device, channel, timeStr, value))
   cnx.commit()
   cnx.close()
