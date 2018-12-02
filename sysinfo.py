#!/usr/bin/env python3
#encoding=UTF-8

import sys, os, time, subprocess, re, threading;
from datetime import datetime, timedelta;

class SysInfo:
    __instance = None;
    __mutex = threading.Lock();
    __re_cpu = re.compile(r'cpu\d+');
    __re_mem_total = re.compile(r'MemTotal:\s*(\d+)');
    __re_mem_free = re.compile(r'MemFree:\s*(\d+)');
    __re_mem_buffers = re.compile(r'Buffers:\s*(\d+)');
    __re_mem_cache = re.compile(r'Cached:\s*(\d+)');
    __itemAvail = {
        'cpu_temp': None,
        'gpu_temp': None,
        'cpu_usage': None,
        'mem_usage': None,
        'boot_time': None,
        'network_stat': None,
    };

    def __cpu_temp(self):
        with open('/sys/class/thermal/thermal_zone0/temp', 'r') as fp:
            result_c = fp.readline();
        return int(float(result_c) / 1000.0);

    def __gpu_temp(self):
        p = subprocess.Popen(['nvidia-smi', '--query-gpu=temperature.gpu', '--format=csv,noheader,nounits'], stdout=subprocess.PIPE);
        return int(p.communicate()[0]);

    def __cpu_usage(self, sample_interval = 0.1):
        with open('/proc/stat', 'r') as fp:
            lines0 = fp.readlines();
        time.sleep(sample_interval);
        with open('/proc/stat', 'r') as fp:
            lines1 = fp.readlines();
        info0 = [int(n) for n in lines0[0].split()[1:]];
        info1 = [int(n) for n in lines1[0].split()[1:]];
        overall = float(sum(info1[0:3]) - sum(info0[0:3])) / float(sum(info1) - sum(info0));

        lines0 = [l.split() for l in lines0];
        lines1 = [l.split() for l in lines1];
        info0, info1, result = [], [], [];
        for l in lines0:
            if None != self.__re_cpu.match(l[0]):
                info0.append([int(n) for n in l[1:]]);
        for l in lines1:
            if None != self.__re_cpu.match(l[0]):
                info1.append([int(n) for n in l[1:]]);
        for i in range(len(info0)):
            total = sum(info1[i]) - sum(info0[i]);
            if 0 == total:
                result.append(0.0);
            else:
                result.append(float(sum(info1[i][0:3]) - sum(info0[i][0:3])) / float(total));
        return {'overall' : overall, 'each' : tuple(result)};

    def __mem_usage(self):
        with open('/proc/meminfo', 'r') as fp:
            meminfo = fp.readlines();
        total, avail = 1, 0;
        for line in meminfo:
            m_total = self.__re_mem_total.match(line);
            m_free = self.__re_mem_free.match(line);
            m_buffers = self.__re_mem_buffers.match(line);
            m_cache = self.__re_mem_cache.match(line);
            if None != m_total:
                total = int(m_total.groups()[0]);
            elif None != m_free:
                avail += int(m_free.groups()[0]);
            elif None != m_buffers:
                avail += int(m_buffers.groups()[0]);
            elif None != m_cache:
                avail += int(m_cache.groups()[0]);
        return float(total-avail)/float(total);

    def __network_stat(self):
        result = {};
        for device in os.listdir('/sys/class/net/'):
            result[device] = {};
            with open(os.path.join('/sys/class/net/', device, 'statistics/rx_bytes')) as f:
                result[device]['rx_bytes'] = int(f.read());
            with open(os.path.join('/sys/class/net/', device, 'statistics/rx_packets')) as f:
                result[device]['rx_packets'] = int(f.read());
            with open(os.path.join('/sys/class/net/', device, 'statistics/tx_bytes')) as f:
                result[device]['tx_bytes'] = int(f.read());
            with open(os.path.join('/sys/class/net/', device, 'statistics/tx_packets')) as f:
                result[device]['tx_packets'] = int(f.read());
        return result;

    def __boot_time(self):
        with open('/proc/uptime', 'r') as fp:
            uptime = fp.readline();
        uptime = float(uptime.split()[0]);
        return (datetime.now() - timedelta(0, uptime));

    def __battery_level(self):
        return self.__droid.batteryGetLevel()[1];

    def __init__(self):
        checklist = {
            'cpu_temp' : self.__cpu_temp,
            'gpu_temp' : self.__gpu_temp,
            'cpu_usage' : self.__cpu_usage,
            'mem_usage' : self.__mem_usage,
            'boot_time' : self.__boot_time,
            'network_stat' : self.__network_stat,
        };
        for k in checklist:
            try:
                checklist[k]();
                self.__itemAvail[k] = checklist[k];
            except Exception as e:
                self.__itemAvail[k] = None;
        try:
            import android;
            self.__droid = android.Android();
            self.__droid.batteryStartMonitoring();
            self.__itemAvail['battery_level'] = self.__battery_level;
        except:
            self.__droid = None;
            self.__itemAvail['battery_level'] = None;

    def __del__(self):
        self.__mutex.acquire();
        if self.__droid != None:
            self.__droid.batteryStopMonitoring();
        self.__mutex.release();

    def __new__(cls, *args, **kwd):
        if SysInfo.__instance is None:
            SysInfo.__instance=object.__new__(cls,*args,**kwd);
        return SysInfo.__instance;

    def fetch(self):
        self.__mutex.acquire();
        result = {};
        for k in self.__itemAvail:
            if (None == self.__itemAvail[k]):
                continue;
            try:
                result[k] = self.__itemAvail[k]();
            except:
                pass;
        self.__mutex.release();
        return result;

