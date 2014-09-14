#!/usr/bin/python
#encoding=UTF-8
config = {
	'fg_url': 'localhost',
	'fg_port': 5401,
	'import_path': '/usr/local/bin',
	'emailremote_conf': '/home/frank/.EmailRemote.conf'
}

import sys, StringIO, time, locale, datetime;
from FlightGear import *;
sys.path.append(config['import_path']);
import EmailRemote;

def send_mail(subject, msg_text, attachments = None):
	conn = EmailRemote.getClient(config['emailremote_conf']);
	try:
		data = {'subject': subject, 'body': msg_text};
		if (None != attachments):
			data['attachments'] = attachments;
		conn.send(data);
	except Exception, e:
		print(str(e));
	finally:
		conn.send(None);
		conn.close();

def is_paused(fg):
	if (fg['/sim/freeze/clock'] == 1 and fg['/sim/freeze/master'] == 1):
		return True;
	else:
		return False;

def get_report(fg):
	report = StringIO.StringIO();

	report.write(fg['/sim/description'] + '\n');
	report.write('\n');

	time_utc = datetime.datetime(int(fg['/sim/time/utc/year']), int(fg['/sim/time/utc/month']), int(fg['/sim/time/utc/day']),
		int(fg['/sim/time/utc/hour']), int(fg['/sim/time/utc/minute']), int(fg['/sim/time/utc/second']));
	report.write('Time: %s UTC\n' % time_utc.strftime('%x %H:%M:%S'));
	time_local = time_utc + datetime.timedelta(seconds = int(fg['/sim/time/local-offset']));
	report.write('Local Time: %s\n' % time_local.strftime('%x %X'));
	report.write('\n');

	report.write('Longitude: %s\n' % fg['/position/longitude-string']);
	report.write('Latitude: %s\n' % fg['/position/latitude-string']);
	report.write('Altitude: %.2fft\n' % float(fg['/position/altitude-ft']));
	report.write('Above ground level: %.2fft\n' % float(fg['/position/altitude-agl-ft']));
	velocity = int(fg['/velocities/groundspeed-kt']);
	if ('ufo' == fg['/sim/flight-model']):
		velocity = int(fg['/velocities/equivalent-kt']);
	report.write('Velocity: %dkts\n' % velocity);
	report.write('\n');

	dist_remaining = float(fg['/autopilot/route-manager/distance-remaining-nm']);
	dist_total = float(fg['/autopilot/route-manager/total-distance']);
	dist_elapsed = dist_total - dist_remaining;
	report.write('Total Distance: %.1fnmi\n' % dist_total);
	report.write('Total Remaining Distance: %.1fnmi\n' % dist_remaining);
	report.write('Total Elapsed Distance: %.1fnmi\n' % dist_elapsed);
	time_elapsed = str(datetime.timedelta(seconds = int(fg['/autopilot/route-manager/flight-time'])));
	report.write('Flight Time: %s\n' % time_elapsed);
	time_remaining = str(datetime.timedelta(seconds = int(fg['/autopilot/route-manager/ete'])));
	report.write('Total Time Remining: %s\n' % time_remaining);

	if ('ufo' != fg['/sim/flight-model']):
		report.write('\n');
		report.write('Fuel Remaining: %.2f pounds / %.2f gallons / %.1f%%\n' % (
			float(fg['/consumables/fuel/total-fuel-lbs']),
			float(fg['/consumables/fuel/total-fuel-gal_us']),
			float(fg['/consumables/fuel/total-fuel-norm']) * 100
		));

	if (fg['/instrumentation/gps/wp/wp[1]/valid'] > 0):
		report.write('\n');
		report.write('Next Destination: %s\n' % fg['/instrumentation/gps/wp/wp[1]/ID']);
		report.write('Distance remaining: %.1fnmi\n' % float(fg['/instrumentation/gps/wp/wp[1]/distance-nm']));
		report.write('Time remaining: %s\n' % fg['/instrumentation/gps/wp/wp[1]/TTW']);
		report.write('Bearing: %d\n' % int(fg['/instrumentation/gps/wp/wp[1]/bearing-mag-deg']));

	if (is_paused(fg)):
		report.write('\n');
		report.write('Simulation paused.\n');

	text = report.getvalue();
	report.close();
	return text;

def screenshot(fg):
	try:
		# Do capture screenshot
		if ('<completed>' != fg.run('screen-capture')):
			return None;

		# Waiting for screenshot finishes
		if (fg['/sim/freeze/clock'] == 1):
			time.sleep(2);
		else:
			while (fg['/sim/freeze/master'] == 1):
				time.sleep(0.2);

		# Return the path of screenshot
		return fg['/sim/paths/screenshot-last'];
	except Exception, e:
		print str(e);
		return None;

if __name__ == '__main__':
	if (len(sys.argv) < 2):
		sys.stderr.write('Usage: %s report|screenshot\n' % sys.argv[0]);
		exit(1);
	command = sys.argv[1];

	try:
		locale.setlocale(locale.LC_ALL, '');
		fg = FlightGear(config['fg_url'], config['fg_port']);
		if ('report' == command):
			print get_report(fg);
		elif ('screenshot' == command):
			imgfile = screenshot(fg);
			if None == imgfile:
				print('Failed to capture screenshot.');
			else:
				send_mail('FlightGear', get_report(fg), [imgfile]);
				print('Mail sent.');
		fg.quit();
	except Exception, e:
		print(str(e));
	exit(0);

