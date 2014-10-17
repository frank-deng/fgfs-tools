#!/bin/bash

ADDRESS=${FG_TELNET%:*};
PORT=${FG_TELNET##*:};
#CMD_QUIT='quit\r\n';

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

function toggle_sound(){
	SOUND=$(fg_get '/sim/sound/enabled');
	if [[ 'true' = $SOUND ]]; then
		fg_set '/sim/sound/enabled' 'false';
	else
		fg_set '/sim/sound/enabled' 'true';
	fi;
}
function get_report(){
	fg_get '/sim/description';

	echo '';
	echo $(fg_get\
		'/sim/time/real/year' '/sim/time/real/month' '/sim/time/real/day'\
		'/sim/time/real/hour' '/sim/time/real/minute' '/sim/time/real/second'\
		'/sim/time/utc/year' '/sim/time/utc/month' '/sim/time/utc/day'\
		'/sim/time/utc/hour' '/sim/time/utc/minute' '/sim/time/utc/second'\
		'/sim/flight-model'\
		'/position/latitude-deg' '/position/longitude-deg'\
		'/position/altitude-ft' '/position/altitude-agl-ft'\
		'/velocities/groundspeed-kt' '/velocities/equivalent-kt'\
		'/autopilot/route-manager/total-distance'\
		'/autopilot/route-manager/distance-remaining-nm'\
		'/autopilot/route-manager/flight-time'\
		'/autopilot/route-manager/ete'\
		'/consumables/fuel/total-fuel-lbs'\
		'/consumables/fuel/total-fuel-gal_us'\
		'/consumables/fuel/total-fuel-norm'\
		'/sim/time/local-offset'\
	) | awk '{
		printf("Real-world time: %4d-%02d-%02d %02d:%02d:%02d\n",
			$1,$2,$3,$4,$5,$6);
		printf("UTC time: %4d-%02d-%02d %02d:%02d:%02d\n",
			$7,$8,$9,$10,$11,$12);
		local_hour = $10 + $27/3600;
		local_hour = (local_hour < 0 ? 24 + local_hour : local_hour);
		printf("Local time: %02d:%02d:%02d\n",local_hour,$11,$12);
		print "";

		model=$13;
		printf("Latitude: %.6f\n", $14);
		printf("Longitude: %.6f\n", $15);
		printf("Altitude: %.2fft\n", $16);
		printf("Above ground level: %.2fft\n", $17);
		printf("Velocity: %dkts\n", ("ufo" == $model ? $18 : $19));

		print "";
		printf("Total Distance: %.1fnmi\n", $20);
		printf("Total Remaining Distance: %.1fnmi\n", $21);
		printf("Total Elapsed Distance: %.1fnmi\n", $20 - $21);
		printf("Flight Time: %02d:%02d:%02d\n", $22/3600, ($22/60)%60, $22%60);
		printf("Time Remining: %02d:%02d:%02d\n", $23/3600, ($23/60)%60, $23%60);

		if ("ufo" != model) {
			print "";
			printf("Fuel Remaining: %.2f pounds / %.2f gallons / %.1f%%\n", $24, $25, $26 * 100);
		}
	}';

	if is_paused; then
		echo -ne '\nSimulation paused.\n';
	fi;
}

if [[ -z $1 ]]; then
	echo "Usage: $0 report|toggle-sound"
	exit 1;
else
	case $1 in
		report)
			get_report;
		;;
		toggle-sound)
			toggle_sound;
		;;
	esac;
fi;
exit 0;

