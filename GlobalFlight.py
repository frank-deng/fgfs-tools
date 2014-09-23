#!/usr/bin/python
#encoding=UTF-8

import sys, time;

from FlightGear import FlightGear;
from FGTools import *;

fnd_dest_name = {
	'KHAF':'Half Moon Bay',
	'KSFO':'San Francisco',
	'KJFK':'New York',
	'HECA':'Cairo',
	'EGLL':'London',
	'OMDB':'Dubai',
	'WSSS':'Singapore',
	'VHHH':'Hong Kong',
	'ZSNJ':'Nanjing',
	'ZSPD':'Shanghai',
	'RJAA':'Tokyo',
	'YSSY':'Sydney',
	'PHNL':'Honolulu',

	'KXTA':'Area 51',
	'KXMR':'Cape Canaveral',
	'HLLT':'Tripoli',
	'ORBS':'Baghdad',
	'OIIE':'Tehran',
	'OAKB':'Kabul',
	'VIDP':'New Delhi',
	'VECC':'Kolkata',
	'VYEL':'Naypyitaw',
	'VDPP':'Phnom Penh',
	'RPMD':'Davao',
	'AYTK':'Rabaul',
	'AGGH':'Guadalcanal',
	'PMDY':'Midway',
}
subject = 'FlightGear: ';
mail_sent = False;

def main_loop(fg):
	global mail_sent;

	if (is_paused(fg)):
		send_mail(subject, "Mission completed, ready for landing.\n\n" + get_report(fg));
		return False;

	airport = fg['/autopilot/route-manager/wp/id'];
	dist = float(fg['/autopilot/route-manager/wp/dist']);
	try:
		if (dist < 1 and (not mail_sent)):
			send_mail(subject,
				('Arrived at %s.\n\n' % fnd_dest_name.get(airport, airport)) + get_report(fg), [screenshot(fg)]);
			mail_sent = True;
		elif (dist >= 1):
			mail_sent = False;
	except ValueError:
		pass;
	return True;

if __name__ == '__main__':
	running = True;
	try:
		fg = FlightGear(config['fg_url'], config['fg_port']);
		subject += fg['/sim/description'];
		while running:
			try:
				running = main_loop(fg);
				time.sleep(1);
			except KeyboardInterrupt, SystemExit:
				running = False;
			except IndexError:
				fg.quit();
				fg = FlightGear(config['fg_url'], config['fg_port']);
		fg.quit();
	except Exception, e:
		send_mail('FlightGear: ERROR', str(e));
		exit(1);
	exit(0);

