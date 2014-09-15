fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

Email sending functionality requires running __EmailRemote__ first.

Screenshot functionality requires __screenshot_command.nas__ to be installed. See section __Accessories__ for detail.

Useful Tools
------------

#### \* FlightGear.py

A python library to access and manipulate the Property Tree of FlightGear via telnet.

#### \* FGTools.py

This program includes the following functions:

* Email sending
* Report generation
* Screenshot capture and email

Synopsis:

	FGTools.py report|screenshot

* __report__  
  Print an report of current state.
* __screenshot__  
  Capture screenshot, then email the screenshot as well as the report generated at the point the screenshot captured.

This program can also be used as a python library for sending email and generating report.

#### \* 777-300ER.py

A daemon program to detect the following conditions:

* Simulation paused and waiting for manual operation.
* Airliner out of fuel.
* Airliner crashed.

If one of the above conditions happend, an email will be sent and the daemon program will exit.

The email sent contains a description of the condition happend, a report generated when the condition happend.

#### \* GlobalFlight.py

A daemon program to detect the following conditions:

* Simulation paused and waiting for manual operation.  
  \* When this happend, an email will be sent and the daemon program will exit.  
  \* The email sent contains a description of the condition happend, a report generated when the condition happend.
* When the aircraft reached a waypoint, get its name and send an E-mail.  
  \* The email sent contains a description of the condition happend, a report generated when the condition happend, a screenshot captured when the condition happend.

Accessories
-----------

All the files listed here are in the directory **accessories**.

#### \* accessories/777-300_frank.patch

Patch for [__777-300__](https://code.google.com/p/b773-flightgear/) and [__777-300ER__](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Some modifications to both 777-300 and 777-300ER
* Descending manager for both 777-300 and 777-300ER. For detail, see section __Descending Manager__
* Refueling function for 777-300ER, based on the distance of the route.

#### \* accessories/bluebird_U-2.patch

Patch for adding 'Drift scope view' for [__Bluebird Explorer Hovercraft__](http://seahorsecorral.org/data/718dd11bcecce7dd0546f98004d26a2d/bluebird-10.92.zip). So it could work as a reconnaissance aircraft like the Lockheed U-2S.

#### \* accessories/system.fgfsrc

This file contains the FlightGear command line options for:

* An ideal flight environment.
* Telnet configuration for accessing the Property Tree.

Copy it to __$FG_DATA__ to activate it.

#### \* accessories/pause_manager

A directory contains file for Pause Manager, see section __Pause Manager__ for detail.

Pause Manager
-------------

Pause the simulation when the remaining distance is shorter than a given value (in nautical miles).

Install: see **install.txt** in the directory **accessories/pause_manager**.

Descending Manager
------------------

When the remaining distance is shorter than a given value (in nautical miles).

* Set vertical speed to -2000ft/min then switch altitude mode to V/S.

When descend to an altitude lower than a given value (in feets).

* Switch altitude mode from V/S to FLCH.
* Switch speed brake mode from off to auto.
* Pause simulation and waiting for manual operation.

Installation: This was included in the patch file **accessories/777-300_frank.patch**.

PHP Interface for FlightGear
----------------------------

#### Description

View FlightGear report, control your aircraft via your browser.

PHP is used to access and manipulate the Property Tree of FlightGear via telnet.

Available for console-based browsers like [w3m](http://w3m.sourceforge.net/), [lynx](http://lynx.isc.org/), [retawq](http://retawq.sourceforge.net/).

#### Install

	cp fg-www/* /var/www

`/var/www` is the root directory of your apache server.

#### Filelist

File  | Description
----- | -----------
777-300.php  | Extra feature for _777-300ER_
bluebird.php  | Extra feature for _bluebird_
config.php  | Configuration information
fail.php  | Show error message when error occurred
FGTelnet.php  | Telnet tool to access Property Tree of FlightGear.
FGTools.php  | Extra functions and classes.
generic.php  | Extra feature for other aircrafts.
index.php  | The main page, including report and extra feature.
sound.php  | Mute or unmute sound of FlightGear

