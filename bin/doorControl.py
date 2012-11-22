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

dev = '/dev/ttyUSB0'
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


def deviceLookUp(deviceToken):
	log("token "+deviceToken)
	#queryString = 'select devicename from IOSpushDevices where devicetoken = "'+ token[:-1]+'"'
	queryString = """select devicename from IOSpushDevices where devicetoken = '%s'""" % deviceToken[:-1]
	log(queryString)
	x.execute(queryString)
	if x.rowcount > 0:
		row = x.fetchone()
		devId = row[0]
		#print devId
		return devId

		


def log(output):
	f = open("/home/doorcontrol/public_html/log", 'a')
	f.write(output)
	f.close()

print "how many? ",is_running()
#exit()


# fork a child process, exit the parent!!!


if(is_running() == 1):
	log("first run. Daemonizing\n")

	filename = '/home/doorcontrol/bin/fifo'
	try:
		os.mkfifo(filename)
	except OSError, e:
		log("failes to create FIFO %s" %e)
	#else:
		#fifo = open(filename, 'w')
	#print filename	
	pid = os.fork()
	log("I am "+str(pid)+"\n")
	if pid > 0:
		sys.exit(0)

	elif pid == 0:
		os.setsid()


else:
	log(str(os.getpid())+"\n")
	if len(sys.argv)>2:
		output = str(datetime.datetime.now()) + " - " + sys.argv[1] + " - " + sys.argv[2] + "\n"
		log(output+"\n")
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
		log(output)
		sys.exit()
	exit()




ser = serial.Serial(dev, rate)

conn = MySQLdb.connect("localhost", "devicemanager", "managedevice", "pushdevices")
x=conn.cursor()

fifoIn = open('/home/doorcontrol/bin/fifo', 'r+')
log("reading from serial\n")

if ser:
	while True:
		output =  ser.readline()

		log(output[:-2]+"\n")

		if output == "exit\r\n":
            		log("got exit command\n")
            		ser.close()
            		conn.close()
			fifoIn.close()
            		exit()
		elif output == "button:lockState:true\r\n":
			token = "0001\n"
			devId = deviceLookUp(token)
            		x.execute("CALL ToggleLock('Main',1, '"+devId+"')")
			output = str(datetime.datetime.now()) + " - L - " + token + "\n"
			log(output)
		elif output == "button:lockState:false\r\n":
			token = "0001\n"
			devId = deviceLookUp(token)
            		x.execute("CALL ToggleLock('Main',0, '"+devId+"')")
			output = str(datetime.datetime.now()) + " - U - " + token + "\n"
			log(output)
		elif output == "lockState:true\r\n":
			token = fifoIn.readline()
			devId = deviceLookUp(token)
			querystring = """CALL ToggleLock('Main',1, "%s")""" 
            		x.execute(querystring, devId)
            		conn.commit()
        	elif output == "lockState:false\r\n":
			token = fifoIn.readline()
			devId = deviceLookUp(token)
			querystring = """CALL ToggleLock('Main',0, "%s")"""
            		x.execute(querystring, devId)
            		conn.commit()
		for lines in os.popen("php /home/doorcontrol/public_html/admin/notify.php"):
			log(lines)
else:
	print "no serial, exiting"
	conn.close()
	exit()
