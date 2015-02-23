#!/bin/bash
fgfs\
	--fg-scenery="$FG_ROOT/Scenery_2.0"\
	--aircraft=dragonfly\
	--airport=khaf\
	--timeofday=morning\
	--disable-real-weather-fetch;
fgfs\
	--fg-scenery="$FG_ROOT/Scenery"\
	--show-aircrafts &>/dev/null;

