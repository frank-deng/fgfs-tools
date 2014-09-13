#!/usr/bin/python
#encoding=UTF-8
config = {
	'fg_url': 'localhost',
	'fg_port': 5401,
	'import_path': '/usr/local/bin',
	'emailremote_conf': '/home/frank/.EmailRemote.conf'
}

import sys, time, locale, datetime;
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
	report = '';

	time_utc = datetime.datetime(
		int(fg['/sim/time/utc/year']), int(fg['/sim/time/utc/month']),
		int(fg['/sim/time/utc/day']),
		int(fg['/sim/time/utc/hour']), int(fg['/sim/time/utc/minute']),
		int(fg['/sim/time/utc/second']),
	);
	time_local = time_utc + datetime.timedelta(seconds = int(fg['/sim/time/local-offset']));

	longitude = fg['/position/longitude-string'];
	latitude = fg['/position/latitude-string'];
	altitude = float(fg['/position/altitude-ft']);
	if ('ufo' == fg['/sim/flight-model']):
		velocity = float(fg['/velocities/equivalent-kt']);
	else:
		velocity = float(fg['/velocities/groundspeed-kt']);

	dist_remaining = float(fg['/autopilot/route-manager/distance-remaining-nm']);
	dist_total = float(fg['/autopilot/route-manager/total-distance']);
	dist_elapsed = float(dist_total) - float(dist_remaining);

	time_elapsed = str(datetime.timedelta(seconds\
			= int(fg['/autopilot/route-manager/flight-time'])));
	time_remaining = str(datetime.timedelta(seconds\
			= int(fg['/autopilot/route-manager/ete'])));

	report += fg['/sim/description'] + '\n';
	report += '\n';
	report += 'Time: %s UTC\n'%(time_utc.strftime('%x %H:%M:%S'));
	report += 'Local Time: %s\n'%(time_local.strftime('%x %X'));
	report += '\n';
	report += 'Longitude: %s\n'%(longitude);
	report += 'Latitude: %s\n'%(latitude);
	report += 'Altitude: %.2fft\n'%(altitude);
	report += 'Velocity: %.2fknots\n'%(velocity);
	report += '\n';
	report += 'Total Distance: %.2fnm\n'%(dist_total);
	report += 'Total Remaining Distance: %.2fnm\n'%(dist_remaining);
	report += 'Total Elapsed Distance: %.2fnm\n'%(dist_elapsed);
	report += 'Flight Time: %s\n'%(time_elapsed);
	report += 'Total Time Remining: %s\n'%(time_remaining);

	if ('ufo' != fg['/sim/flight-model']):
		report += '\n';
		report += 'Fuel Remaining: %.2f pounds / %.2f gallons / %.1f%%\n' % (
				float(fg['/consumables/fuel/total-fuel-lbs']),
				float(fg['/consumables/fuel/total-fuel-gal_us']),
				float(fg['/consumables/fuel/total-fuel-norm']) * 100
			);

	if (fg['/instrumentation/gps/wp/wp[1]/valid'] > 0):
		next_wp = fg['/instrumentation/gps/wp/wp[1]/ID'];
		next_wp_dist = float(fg['/instrumentation/gps/wp/wp[1]/distance-nm']);
		next_wp_ttw = fg['/instrumentation/gps/wp/wp[1]/TTW'];
		bearing = float(fg['/instrumentation/gps/wp/wp[1]/bearing-mag-deg']);

		report += '\n';
		report += 'Next Destination: %s\n'%(next_wp);
		report += 'Distance remaining: %.2fnm\n'%(next_wp_dist);
		report += 'Time remaining: %s\n'%(next_wp_ttw);
		report += 'Bearing: %.2f\n' % bearing;

	return report;

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

