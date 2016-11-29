# File: data_gen.py
# Author: Matthew Lindly (mlindly) and Samuel Lees (sslees)
# Date: 11/28/16
# Class: CPE 458-01
# Assignment: Final Project

from random import randint

JAN_1_2016_PST = 1451635200
S_PER_LEAP_YR = 31622400
S_PER_5_MIN = 300
S_PER_WK = 604800
MAX_VAL = 1023
VARIANCE = 30

for timestamp in range(JAN_1_2016_PST, JAN_1_2016_PST + S_PER_LEAP_YR,
 S_PER_5_MIN): # measurements every five minutes for all of 2016
   measurement = (S_PER_WK - timestamp % S_PER_WK) * MAX_VAL / S_PER_WK + \
    randint(-VARIANCE, VARIANCE) # weekly watering cycles with variance
   print('{},{}'.format(timestamp, (0 if measurement < 0 else MAX_VAL if
    measurement > MAX_VAL else measurement) / MAX_VAL)) # output percentage
