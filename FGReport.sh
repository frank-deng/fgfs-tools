#!/bin/bash

ADDRESS=${FG_TELNET%:*};
PORT=${FG_TELNET##*:};
#CMD_QUIT='quit/r/n'

function fg_get(){
	COMMAND='';
	for PROP in $@; do
		COMMAND="${COMMAND}get $PROP\\r\\n";
	done
	echo -ne "${COMMAND}$CMD_QUIT" |\
	netcat -i 1 "${ADDRESS}" "${PORT}" |\
	grep -o "'.*'" |\
	awk '{printf("%s\n",substr($0,2,length($0)-2));}'
}
function fg_set(){
	echo -ne "set $1 $2\r\n$CMD_QUIT" |\
	netcat -i 1 "${ADDRESS}" "${PORT}" &>/dev/null
}
function fg_run(){
	echo -ne "run $1\r\n$CMD_QUIT" |\
	netcat -i 1 "${ADDRESS}" "${PORT}" |\
	head -n -1;
}

fg_get\
	'/sim/time/real/year'\
	'/sim/time/real/month'\
   	'/sim/time/real/day'\
	'/sim/description'\
	'/sim/time/real/hour'\
	'/sim/time/real/minute'\
	'/sim/time/real/second';
#fg_set '/sim/aircraft' 'ufo';
#fg_run 'screen-capture';
exit 0;

