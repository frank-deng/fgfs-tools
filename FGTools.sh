#!/bin/bash

ADDRESS=${FG_TELNET%:*};
PORT=${FG_TELNET##*:};
CMD_QUIT='quit\r\n';

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
function is_paused(){
	result=$(fg_get '/sim/freeze/clock' '/sim/freeze/master'|tr '\r\n' ' ');
	if [[ $result = 'true true ' ]]; then
		return 0;
	else
		return 1;
	fi;
}

function set_sound(){
	if [[ 'on' = $1 ]]; then
		fg_set '/sim/sound/enabled' 'true';
	else
		fg_set '/sim/sound/enabled' 'false';
	fi;
}
function get_report(){
	echo -ne "set /sim/signals/fgreport 1\r\nget /sim/fgreport/text\r\n$CMD_QUIT" |\
		netcat -i 1 "${ADDRESS}" "${PORT}" | tail -n +3 | head -n -2
}

if [[ -z $1 ]]; then
	echo "Usage: $0 report|soundon|soundoff"
	exit 1;
else
	case $1 in
		report)
			get_report;
		;;
		soundon)
			set_sound on;
		;;
		soundoff)
			set_sound off;
		;;
	esac;
fi;
exit 0;

