# File: measure.py
# Author: Matthew Lindly (mlindly)
# Date: 11/30/16
# Class: CPE 458-01
# Assignment: Final Project

import requests

vals = "75.0 12 1".split(" ")
#vals = input().split(" ")
url = "https://maker.ifttt.com/trigger/dry/with/key/bLnBZe0-3m5ULHo5F8Hnbp"
payload = {'value1': vals[0], 'value2' : vals[1], 'value3' : vals[2]}
requests.post(url, json = payload)
