#!/usr/bin/env python3

FG_DATA_URL='http://localhost:5410/json/fgreport/';
ENCODING='GB2312';

import time, httplib2, json, sysinfo, sys, os, termios, atexit;

def getFlightData():
    try:
        result = {};
        h = httplib2.Http(timeout=0.8);
        resp, content = h.request(FG_DATA_URL);
        data = json.loads(content.decode('UTF-8'));
        for item in data['children']:
            result[item['name']] = item['value'];
        return result;
    except Exception as e:
        return None;

from select import select;
class Kbhit:
    def __init__(self):
        self.__fd = sys.stdin.fileno();
        self.__new_term = termios.tcgetattr(self.__fd);
        self.__old_term = termios.tcgetattr(self.__fd);
        self.__new_term[3] = (self.__new_term[3] & ~termios.ICANON & ~termios.ECHO);
        self.enable();
        atexit.register(self.disable);

    def enable(self):
        try:
            termios.tcsetattr(self.__fd, termios.TCSANOW, self.__new_term);
        except Exception as e:
            print(str(e));
            pass;
        
    def disable(self):
        try:
            termios.tcsetattr(self.__fd, termios.TCSANOW, self.__old_term);
        except Exception as e:
            print(str(e));
            pass;

    def kbhit(self):
        dr,dw,de = select([sys.stdin], [], [], 0);
        return len(dr)>0;

    def getch(self):
        try:
            ch = sys.stdin.read(1);
            if ch == '\x00' or ord(ch) >= 0xA1:
                return ch+sys.stdin.read(1);
            else:
                return ch;
        except Exception as e:
            return None;

class Console(Kbhit):
    __out = sys.stdout.fileno();
    def __init__(self, encoding):
        Kbhit.__init__(self);
        self.__encoding = encoding;

    def write(self, text):
        if (isinstance(text, str)):
            os.write(self.__out, text.encode(self.__encoding, 'ignore'));
        else:
            os.write(self.__out, text);
    
    def writeln(self, text):
        if (isinstance(text, str)):
            os.write(self.__out, text.encode(self.__encoding, 'ignore')+b'\r\n');
        else:
            os.write(self.__out, text+b'\r\n');

    def clrscr(self):
        os.write(self.__out, b'\x1b[2J\x1b[1;1H\x1b[0m');

    def clrline(self, lineNum):
        os.write(self.__out, b'\x1b[%dK'%(lineNum+1));

    def setCursorPos(self, x, y):
        os.write(self.__out, b'\x1b[%d;%dH'%(x+1,y+1));

    def addstr(self, x, y, text, bold=False, fg=None, bg=None):
        if (bold):
            os.write(self.__out, b'\x1b[1m');
        if (None != fg):
            os.write(self.__out, b'\x1b[%dm'%(fg+30));
        if (None != bg):
            os.write(self.__out, b'\x1b[%dm'%(bg+40));
        self.setCursorPos(x, y);
        self.write(text);
        os.write(self.__out, b'\x1b[0m');

class TXFgfsView(Console):
    __hasFgData = False;
    __lastTime = None;
    def __init__(self):
        Console.__init__(self, ENCODING);
        self.clrscr();
        self.__drawFrame();

    def close(self):
        self.clrscr();

    def __drawFrame(self):
        self.addstr(0, 0, " "*79, fg=0, bg=7);
        self.addstr(24, 0, " "*79, fg=0, bg=7);
        self.addstr(0, 0, " FlightGear飞行控制台", fg=4, bg=7);
        self.addstr(1, 2, "CPU温度");
        self.addstr(1, 22, "GPU温度");
        self.addstr(1, 42, "CPU使用率");
        self.addstr(1, 62, "内存使用率");

    def updateTime(self):
        nowTime = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()));
        self.addstr(0, 60, nowTime, fg=0, bg=7);

    def update(self, sysdata, fgdata, redraw = False):
        if (redraw):
            self.__drawFrame();

        self.addstr(2, 2, '%d C  '%sysdata['cpu_temp']);
        self.addstr(2, 22, '%d C  '%sysdata['cpu_temp']);
        self.addstr(2, 42, '%.1f%%  '%(sysdata['cpu_usage']['overall'] * 100));
        self.addstr(2, 62, '%.1f%%  '%(sysdata['mem_usage'] * 100));

        if None == fgdata:
            if self.__hasFgData:
                self.addstr(3, 0, ' '*79);
                for n in range(8):
                    self.addstr(n+4, 0, ' '*79);
            self.addstr(24, 1, '（无飞行任务）', fg=0, bg=7);
            self.__hasFgData = False;
            return;

        if not self.__hasFgData:
            self.addstr(3, 0, '━'*39);
            self.addstr(4, 2, '机型：');
            self.addstr(5, 2, '经度：');
            self.addstr(6, 2, '纬度：');
            self.addstr(7, 2, '飞行时间：');
            self.addstr(8, 2, '剩余时间：');
            self.addstr(9, 2, '总里程：');
            self.addstr(10,2, '剩余里程：');
            self.addstr(11,2, '已飞行里程：');
            self.__hasFgData = True;

        if fgdata['longitude-deg'] >= 0:
            fgdata['longitude'] = " %.6fE   "%(abs(fgdata['longitude-deg']));
        else:
            fgdata['longitude'] = " %.6fW   "%(abs(fgdata['longitude-deg']));

        if fgdata['latitude-deg'] >= 0:
            fgdata['latitude'] = " %.6fN   "%(abs(fgdata['latitude-deg']));
        else:
            fgdata['latitude'] = " %.6fS   "%(abs(fgdata['latitude-deg']));
        self.addstr(4, 22, '%s         '%fgdata['aircraft']);
        self.addstr(5, 21, '%s         '%fgdata['longitude']);
        self.addstr(6, 21, '%s         '%fgdata['latitude']);
        self.addstr(7, 22, '%s         '%fgdata['flight-time-string']);
        self.addstr(8, 22, '%s         '%fgdata['ete-string']);
        self.addstr(9, 22, '%.1fnm       '%fgdata['total-distance']);
        self.addstr(10,22, '%.1fnm       '%fgdata['distance-remaining-nm']);
        self.addstr(11,22, '%.1fnm       '%(fgdata['total-distance'] - fgdata['distance-remaining-nm']));

        if fgdata['crashed']:
            statusText = '已坠毁        ';
            self.addstr(24, 1, statusText, fg=1, bg=7);
        elif fgdata['paused']:
            statusText = '已暂停        ';
            self.addstr(24, 1, statusText, fg=4, bg=7);
        else:
            statusText = '飞行中        ';
            self.addstr(24, 1, statusText, fg=2, bg=7);


if __name__ == '__main__':
    running = True;
    view = TXFgfsView();
    tick = 0;
    lastTime = None;
    try:
        while running:
            if (tick % 4 == 0):
                view.update(sysinfo.SysInfo().fetch(), getFlightData());
            view.updateTime();
            tick += 1;
            time.sleep(0.25);
            if view.kbhit() and '\x1b' == view.getch():
                running = False;
    except KeyboardInterrupt:
        pass;
    finally:
        view.close();
    exit(0);

