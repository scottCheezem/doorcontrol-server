#!/usr/bin/env python
#file: doorControl.py

#open a persistant connection to the device
#commands come in via writing to /dev/device
#when a command comes in write directly to db
#trigger a push with lock state payload?


import serial
import time
import sys
import os
import MySQLdb


def is_running():
	running = 0;
	for lines in os.popen("ps aux"):
#		print lines.find(sys.argv[0])
		if lines.find(sys.argv[0]) > 0:
			fields = lines.split()
			proc = fields[1]
			running +=1
			#print proc 
	return running




print "how many? ",is_running()
#exit()


# fork a child process, exit the parent!!!


if(is_running() == 1):
	print "forking"	
	pid = os.fork()
	if pid > 0:
		sys.exit(0)
	elif pid == 0:
		os.chdir("/")
		os.setsid()
		os.umask(0)



else:
	exit()


dev = '/dev/ttyUSB1'
rate = 9600

if len(sys.argv)>1:
	if sys.argv[1] == "L":
		f = open(dev, 'w')
		f.write(sys.argv[1])
		f.close()
		sys.exit()

#os.chdir("/")
#os.setsid()
#os.umask(0)


ser = serial.Serial(dev, rate)

conn = MySQLdb.connect("loaclhost", "devicemanager", "managedevice", "pushdevices")
x=conn.cursor()

if ser:
	while True:
		output =  ser.readline()
#ok now write to somewhere useful like a db
				
#		print output[:-2]
		if output == "exit\r\n":
#            		print "got exit command\n"
            		ser.close()
            		conn.close()
            		exit()
		elif output == "lockState:true\r\n":
            		x.execute("CALL ToggleLock('Main',1)")
            		conn.commit()
        	elif output == "lockState:false\r\n":
            		x.execute("CALL ToggleLock('Main',0)")
            		conn.commit()
else:
	print "no serial, exiting"
	conn.close()
	exit()