from telnetlib import Telnet
import socket
import re, time
from string import split, join

CRLF = '\r\n'
class FGTelnet(Telnet):
	def __init__(self,host = 'localhost',port = 5400):
		self.active = False;
		Telnet.__init__(self,host,port)
		self.prompt = [re.compile('/[^>]*> ')];
		self.timeout = 5
		self.re_parseresp = re.compile( '[^=]*=\s*\'([^\']*)\'\s*([^\r]*)\r');
		#Telnet.set_debuglevel(self,2)
		self.active = True;

	def __del__(self):
		"""Ensure telnet connection is closed cleanly."""
		if self.active:
			self.quit();

	def __getitem__(self,key):
		"""Wrapper of get() method"""
		return self.get(key);

	def __setitem__(self, key, value):
		"""Wrapper of set() method"""
		self.set(key, value);

	def get(self, var):
		"""Retrieve the value of a parameter and convert the value to the equivalent Python type.
		"""
		# Send command and get response
		self._putcmd('get ' + var);
		resp_all = self._getresp();

		# Response is multi-line or contains Apostrophe
		if (len(resp_all) > 1 or resp_all[0].count('\'') > 2):
			self._putcmd('data' + CRLF + 'get ' + var + CRLF + 'prompt');
			return '\n'.join(self._getresp());

		# Parse response
		match = self.re_parseresp.match(resp_all[0] + '\r');
		if not match:
			return None;
		value,type = match.groups();
		if value == '':
			return None
		elif type == '(double)':
			return float(value)
		elif type == '(int)':
			return int(value)
		elif type == '(bool)':
			if value == 'true':
				return True;
			else:
				return False;
		else:
			return value

	def set(self, key, value):
		"""Set variable to a new value"""
		self._putcmd('set ' + key + ' ' + str(value));
		self._getresp() # Discard response

	def run(self, command):
		"""Run fgcommand"""
		self._putcmd('run ' + str(command));
		self._getresp() # Discard response

	def quit(self):
		"""Terminate connection"""
		self._putcmd('quit');
		self.close();
		self.active = False;
 
	# Internal: send one command to FlightGear
	def _putcmd(self,cmd):
		Telnet.write(self, cmd + CRLF);
 
	# Internal: get a response from FlightGear
	def _getresp(self):
		(i,match,resp) = Telnet.expect(self, self.prompt, self.timeout)
		# Remove the terminating prompt.
		# Everything preceding it is the response.
		return split(resp, CRLF)[:-1];
 
