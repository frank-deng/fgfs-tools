#!/usr/bin/env python
from soaplib.wsgi_soap import SimpleWSGISoapApp;
from soaplib.service import soapmethod;
from soaplib.serializers.primitive import String, Integer, Array, Any;
from lxml import etree;
from FGTelnet import FGTelnet;

class FGSoapService(SimpleWSGISoapApp):
	def __init__(self, telnet_host, telnet_port):
		SimpleWSGISoapApp.__init__(self);
		self.conn = FGTelnet(telnet_host, telnet_port);

	def __getType(self, variable):
		if (int == type(variable)):
			return 'int';
		elif (float == type(variable)):
			return 'float';
		elif (str == type(variable)):
			return 'str';
		else:
			return 'unknown';

	@soapmethod(Any,_returns=Any)
	def getProp(self, props):
		props_all = [];
		values_all = [];

		#Fetch props to process
		p = props;
		while p is not None:
			if 'prop' == p.tag:
				props_all.append(p.text);
			p = p.getnext();

		#Fetch values
		try:
			for prop in props_all:
				values_all.append(self.conn[prop]);
		except Exception, e:
			node_error = etree.Element('error');
			node_error.text = str(e);
			return node_error;

		#Prepare element tree
		prop_values = etree.Element('values');
		for i in range(len(values_all)):
			node_value = etree.Element('value');
			node_value.set('n', str(i));
			node_value.set('type', self.__getType(values_all[i]));
			node_value.text = str(values_all[i]);
			prop_values.append(node_value);

		return prop_values;

	@soapmethod(Any,_returns=Any)
	def setProp(self, pairs):
		pairs_all = [];

		#Fetch prop/value pair
		p = pairs;
		while p is not None:
			if 'pair' == p.tag and 'prop' == p[0].tag and 'value' == p[1].tag:
				pairs_all.append((p[0].text, p[1].text));
			p = p.getnext();

		try:
			for prop, val in pairs_all:
				self.conn[prop] = val;
		except Exception, e:
			node_error = etree.Element('error');
			node_error.text = str(e);
			return node_error;

		node_ok = etree.Element('completed');
		node_ok.text = 'Operation completed normally.';
		return node_ok;

if __name__=='__main__':
	try:
		from wsgiref.simple_server import make_server
		server = make_server('localhost', 5431, FGSoapService('localhost', 5401));
		server.serve_forever();
	except KeyboardInterrupt:
		pass;
	except ImportError:
		print "Error: Python >= 2.5 is required.";
		exit(1);
	exit(0);

