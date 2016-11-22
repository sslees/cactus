# File: measure.py
# Author: Matthew Lindly (mlindly)
# Date: 11/16/16
# Class: CPE 458-01
# Assignment: Final Project
# References:
#    http://www.raspberrypi-spy.co.uk/2013/10/
#       analogue-sensors-on-the-raspberry-pi-using-an-mcp3008/

# Monitor moisture sensor on MCP3008 ADC, Channel 0.

import spidev

CHANNEL = 0

spi = spidev.SpiDev()
spi.open(0, 0)

def read_channel(ch):
   adc = spi.xfer2([1, 8 + ch << 4, 0])

   return ((adc[1] & 3) << 8) + adc[2]

print('%04d' % read_channel(CHANNEL))
