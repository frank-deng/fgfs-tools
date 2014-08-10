#!/usr/bin/python
#encoding=UTF-8
config = {
	'fg_url': 'localhost',
	'fg_port': 5401,
	'import_path': '/usr/local/bin',
	'config_file': '/home/frank/.EmailRemote.conf'
}

import sys, time, traceback;
from FlightGear import *;
sys.path.append(config['import_path']);
import EmailRemote;

def send_mail(subject, msg_text, attachments = None):
	try:
		conn = EmailRemote.getClient(config['config_file']);
		try:
			data = {'subject': subject, 'body': msg_text};
			if (None != attachments):
				data['attachments'] = attachments;
			conn.send(data);
		except:
			traceback.print_exc();
		finally:
			conn.send(None);
			conn.close();
	except:
		traceback.print_exc();

def get_report(fg):
	report = '';
	time_local = time.strftime('%I:%M:%S %p',
		time.gmtime(int(fg['/sim/time/utc/day-seconds']) +
			int(fg['/sim/time/local-offset']))
	);
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

	time_elapsed_sec = int(fg['/autopilot/route-manager/flight-time']);
	time_elapsed = str(int(time_elapsed_sec / 60.0 / 60.0)) + time.strftime(':%M:%S', time.gmtime(time_elapsed_sec));
	time_remaining_sec = int(fg['/autopilot/route-manager/ete']);
	time_remaining = str(int(time_remaining_sec / 60.0 / 60.0)) + time.strftime(':%M:%S', time.gmtime(time_remaining_sec));

	report += 'UTC: %s\n'%(fg['/sim/time/gmt'].replace('T', ' '));
	report += 'Local Time: %s\n'%(time_local);
	report += '\n';
	report += 'Longitude: %s\n'%(longitude);
	report += 'Latitude: %s\n'%(latitude);
	report += 'Altitude (ft): %.2f\n'%(altitude);
	report += 'Velocity (kts): %.2f\n'%(velocity);
	report += '\n';
	report += 'Total Distance (nm): %.2f\n'%(dist_total);
	report += 'Total Remaining Distance (nm): %.2f\n'%(dist_remaining);
	report += 'Total Elapsed Distance (nm): %.2f\n'%(dist_elapsed);
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
		report += 'Distance remaining (nm): %.2f\n'%(next_wp_dist);
		report += 'Time remaining (nm): %s\n'%(next_wp_ttw);
		report += 'Bearing: %.2f\n' % bearing;

	return report;

def screenshot(fg):
	try:
		while ('capture' == fg['/command/screenshot']):
			time.sleep(0.1);
		fg['/command/screenshot'] = 'capture';
		while ('capture' == fg['/command/screenshot']):
			time.sleep(0.1);
		result = [fg['/sim/paths/screenshot-last']];
		return result;
	except:
		return None;

if __name__ == '__main__':
	if (len(sys.argv) < 2):
		print('Command to get a report: %s report' % sys.argv[0]);
		print('Command to get a screenshot: %s screenshot' % sys.argv[0]);
		exit(1);
	command = sys.argv[1];
	fg = FlightGear(config['fg_url'], config['fg_port']);
	if ('report' == command):
		print get_report(fg);
	elif ('screenshot' == command):
		send_mail('FlightGear', get_report(fg), screenshot(fg));
		print('Mail sent.');
	fg.quit();
	exit(0);

