#!/bin/bash

if [ -z $1 ]; then
	fgfs --show-aircrafts;
	exit 0;
fi;

fgfs\
	--fg-scenery="$FG_ROOT/Scenery_2.0"\
	--aircraft=${1}\
	--airport=khaf\
	--timeofday=morning\
	--disable-real-weather-fetch;
fgfs\
	--fg-scenery="$FG_ROOT/Scenery"\
	--show-aircrafts &>/dev/null;

