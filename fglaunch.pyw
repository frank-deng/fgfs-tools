#!/usr/bin/env python3
#encoding=UTF-8

import sys, socket, os, re, subprocess, platform;
import xml.etree.ElementTree as ET;

fgfs_possible_exec = [
    os.getenv('FGFS_BIN'),
    'D:\\Program Files\\FlightGear 2020.3.2\\bin\\fgfs.exe',
    '/usr/bin/fgfs',
    '/usr/local/bin/fgfs',
];
pausemgr_dist = 20;

if __name__ == '__main__':
    #Process Command Line
    fp_file = None;
    aircraft = None;
    try:
        fp_file=sys.argv[1];
    except Exception as e:
        print(e);

    #Get runway to takeoff
    try:
        if(fp_file):
            fp_tree = ET.parse(fp_file);
            fp_root = fp_tree.getroot();
            airport = fp_root.findall('departure/airport')[0].text;
            runway = fp_root.findall('departure/runway')[0].text;
            fp_params = [
                '--launcher',
                '--airport='+airport,
                '--runway='+runway,
                '--flight-plan='+os.path.abspath(fp_file),
                '--prop:/autopilot/pausemgr-dist='+str(pausemgr_dist),
            ];

            match=re.search(r'_([^\._]+)\.', fp_file, re.M|re.I);
            if(match):
                aircraft=match.group(1);
    except Exception as e:
        sys.stderr.write('Failed to parse Flight Plan: ' + str(e) + '\n');
    
    if (aircraft):
        fp_params.append('--aircraft=' + aircraft);

    if (re.search(r'747|757',aircraft,re.M|re.I)):
        fp_params.append('--prop:/sim/magicAI=1');

    #Launch FlightGear
    fgfs_exec=None;
    for i in fgfs_possible_exec:
        if i == None:
            continue;
        if os.path.isfile(i):
            fgfs_exec = i;
            break;
    if (fgfs_exec == None):
        sys.stderr.write('Executable of fgfs not found.\n');
        exit(1);

    os.chdir("D:\\Program Files\\FlightGear 2020.3.2\\bin");
    #print(fp_params);
    subprocess.Popen([fgfs_exec] + fp_params);
    exit(0);

