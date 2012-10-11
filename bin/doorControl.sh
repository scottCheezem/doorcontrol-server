#!/bin/bash

arduino=ttyUSB1;

if [[ $1 == '' ]] ; then 
	iam=$(ps ax | grep $arduino | grep -v 'grep' | awk '{print $1}');
	if [[ $iam == '' ]] ; then 
		exit 0;
	else
		echo $iam
	fi;

elif [[ $1 == 'start' ]] ; then
	echo "starting";
	cat /dev/$arduino > /home/user/doorLock.log &

elif [[ $1 == 'stop' ]] ; then

	killMe=$(ps ax | grep $arduino | grep -v 'grep' | awk '{print $1}');
	if [[ $killMe != '' ]] ; then
		echo "stopping" $killMe;
		kill -9 "$killMe";
	fi;
else
	echo $1 > /dev/$arduino ; sleep 1s ; grep locked /home/user/doorLock.log | tail -1 ;

fi
