# File: measure.py
# Author: Matthew Lindly (mlindly) and Samuel Lees (sslees)
# Date: 3/8/17
# Class: CPE 461-17 LAB
# Assignment: Senior Project
# References:
#  http://www.raspberrypi-spy.co.uk/2013/10/
#   analogue-sensors-on-the-raspberry-pi-using-an-mcp3008/

import spidev

def measure(channel):
   spi = spidev.SpiDev()
   spi.open(0, 0)
   adc = spi.xfer2([1, 8 + channel << 4, 0])
   spi.close()

   return ((adc[1] & 3) << 8) + adc[2]
