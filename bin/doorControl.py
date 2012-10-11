

#open a persistant connection to the device
#commands come in via writing to /dev/device
#when a command comes in write directly to db
#trigger a push with lock state payload?


import serial
import time
import sys

dev = '/dev/ttyUSB1'
rate = 9600

ser = serial.Serial(dev, rate)


if ser:
	while True:
		output =  ser.readline()
#ok now write to somewhere useful like a db
				
		print output
#		time.sleep(1)
		if output == "exit\r\n":
			print "got exit command\n"		
			ser.close()
			exit()

