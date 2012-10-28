#!/usr/bin/env python
#file: doorControl.py

#open a persistant connection to the device
#commands come in via writing to /dev/device
#when a command comes in write directly to db
#trigger a push with lock state payload?


import serial
import datetime
#import time
import sys
import os
import MySQLdb

dev = '/dev/ttyUSB1'
rate = 9600

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
	print "first run. Daemonizing"

	filename = '/home/doorcontrol/bin/fifo'
	try:
		os.mkfifo(filename)
	except OSError, e:
		print "failes to create FIFO %s" %e
	#else:
		#fifo = open(filename, 'w')
	print filename	
	pid = os.fork()
	print "I am "+str(pid)
	if pid > 0:
		sys.exit(0)
#	elif pid == 0:
#		os.chdir("/")
#		os.setsid()
#		os.umask(0)



else:
	print os.getpid()
	if len(sys.argv)>2:
		output = str(datetime.datetime.now()) + " - " + sys.argv[1] + " - " + sys.argv[2] + "\n"
		print output
		fifo = open('/home/doorcontrol/bin/fifo', 'w+')
		fifo.write(sys.argv[2])
		fifo.write("\n")
		fifo.close()
	        if sys.argv[1] == "L":
	                f = open(dev, 'w')
	                f.write(sys.argv[1])
	                f.close()
		if sys.argv[1] == "U":
			f = open(dev, 'w')
			f.write(sys.argv[1])
			f.close()
		f = open("/home/doorcontrol/public_html/log", 'a')
		f.write(output)
		f.close()
		sys.exit()
	exit()




ser = serial.Serial(dev, rate)

conn = MySQLdb.connect("localhost", "devicemanager", "managedevice", "pushdevices")
x=conn.cursor()

fifoIn = open('/home/doorcontrol/bin/fifo', 'r+')
print "reading from serial"

if ser:
	while True:
		output =  ser.readline()
		token = fifoIn.readline()
		#fifoIn.close()
		print "token "+token
		queryString = 'select devicename from IOSpushDevices where devicetoken = "'+ token[:-1]+'"'
		
		x.execute(queryString)
		row = x.fetchone()
		devId = row[0]
		print output[:-2]
		if output == "exit\r\n":
            		print "got exit command\n"
            		ser.close()
            		conn.close()
			fifoIn.close()
            		exit()
		elif output == "lockState:true\r\n":
			
            		x.execute("CALL ToggleLock('Main',1, '"+devId+"')")
            		conn.commit()
        	elif output == "lockState:false\r\n":
            		x.execute("CALL ToggleLock('Main',0, '"+devId+"')")
            		conn.commit()
		print "got here\n"
		for lines in os.popen("php /home/doorcontrol/public_html/SimplePush/foo.php"):
			print lines
else:
	print "no serial, exiting"
	conn.close()
	exit()
