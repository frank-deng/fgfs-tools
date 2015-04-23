#!/bin/bash
javac -d . -cp $(echo /usr/share/tomcat7/lib/*.jar | tr ' ' ':'):/var/lib/tomcat7/webapps/fgfs/WEB-INF/lib/FGTelnetConnection.jar ../../src/*.java
