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
      user=DB_USER, password=DB_PASSWORD, database=DB_DATABASE)
   cursor = cnx.cursor()
   cursor.execute(
      "CREATE TABLE IF NOT EXISTS users ("
      "   email VARCHAR(254) PRIMARY KEY,"
      "   first_name VARCHAR(64) NOT NULL,"
      "   last_name VARCHAR(64) NOT NULL,"
      "   password_hash VARCHAR(255) NOT NULL);")
   cursor.execute(
      "CREATE TABLE IF NOT EXISTS devices ("
      "   uuid CHAR(36) PRIMARY KEY,"
      "   ip VARCHAR(39) DEFAULT NULL,"
      "   user VARCHAR(254) DEFAULT NULL,"
      "   nickname VARCHAR(64) DEFAULT NULL,"
      "   FOREIGN KEY (user) REFERENCES users (email) ON UPDATE CASCADE);")
   cursor.execute(
      "CREATE TABLE IF NOT EXISTS measurements ("
      "   id INTEGER AUTO_INCREMENT PRIMARY KEY,"
      "   device CHAR(36) NOT NULL,"
      "   channel INTEGER NOT NULL,"
      "   timestamp DATETIME NOT NULL,"
      "   value INTEGER UNSIGNED NOT NULL,"
      "   INDEX ind_measurements_timestamp (device, channel, timestamp),"
      "   FOREIGN KEY (device) REFERENCES devices (uuid) ON UPDATE CASCADE);")
   cnx.close()

def handle_device(uuid, ip):
   cnx = mysql.connector.connect(
      user=DB_USER, password=DB_PASSWORD, database=DB_DATABASE)
   cursor = cnx.cursor()
   cursor.execute('INSERT INTO devices (uuid, ip) VALUES (%s, %s) '
      'ON DUPLICATE KEY UPDATE ip=VALUES(ip);', (uuid, ip))
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
