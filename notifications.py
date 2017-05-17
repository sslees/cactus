# File: notifications.py
# Author: Matthew Lindly (mlindly) and Samuel Lees (sslees)
# Date: 4/1/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project

import requests

def notify_dry():
   requests.post(
    'https://maker.ifttt.com/trigger/dry/with/key/qGhtdMabf2zxRsrpVc2P6')

def notify_watered():
   requests.post(
    'https://maker.ifttt.com/trigger/watered/with/key/qGhtdMabf2zxRsrpVc2P6')
