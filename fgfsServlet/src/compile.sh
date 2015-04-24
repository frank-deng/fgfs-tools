#!/bin/bash
CLASSPATH=$(echo /usr/share/tomcat7/lib/*.jar ../WEB-INF/lib/*.jar| tr ' ' ':')
javac\
	-d '../WEB-INF/classes'\
	-cp $CLASSPATH\
	*.java

