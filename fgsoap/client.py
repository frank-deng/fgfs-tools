#!/usr/bin/env python

SOAP_GETPROP='''
<SOAP:ENVELOPE
	xmlns:SOAP="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
>
	<SOAP:BODY>
		<getProp>
			<props>
				<prop>/autopilot/route-manager/ete</prop>
				<prop>/sim/description</prop>
				<haha>Frank<b>Bold</b>123456</haha>
			</props>
		</getProp>
	</SOAP:BODY>
</SOAP:ENVELOPE>
'''
SOAP_SETPROP='''
<SOAP:ENVELOPE
	xmlns:SOAP="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
>
	<SOAP:BODY>
		<setProp>
			<pairs>
				<pair>
					<prop>/command/fgreport</prop>
					<value>Frank</value>
				</pair>
			</pairs>
		</setProp>
	</SOAP:BODY>
</SOAP:ENVELOPE>
'''
import httplib;
conn = httplib.HTTPConnection("localhost", 5410)
conn.request('POST', '/wsdl', SOAP_GETPROP)
response = conn.getresponse();
print response.read();
conn.close();

