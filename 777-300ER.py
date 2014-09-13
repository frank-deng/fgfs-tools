#!/usr/bin/python
#encoding=UTF-8
'''
Cruise: 35000ft, 280kt IAS
'''

import time;

from FlightGear import FlightGear;
from FGTools import *;

subject = 'FlightGear: 777-300ER';
text = {
	'crashed':'777-300ER has crashed.',
	'outoffuel':'777-300ER has no fuel now.',
	'paused':'Simulation paused, waiting for manual operation.',
};

def main_loop(fg):
	if (fg['/sim/crashed'] > 0):
		send_mail(subject, text['crashed'] + "\n\n" + get_report(fg));
		return False;
	elif (float(fg['/consumables/fuel/total-fuel-norm']) < 0.0001):
		send_mail(subject, text['outoffuel'] + "\n\n" + get_report(fg));
		return False;
	elif (is_paused(fg)):
		send_mail(subject, text['paused'] + "\n\n" + get_report(fg));
		return False;

	return True;

if __name__ == '__main__':
	running = True;
	try:
		fg = FlightGear(config['fg_url'], config['fg_port']);
		fg['/autopilot/settings/descending-manager-running'] = 1;
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

