fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### fgreport.nas

A FlightGear Nasal script used to generate report.

__Install:__ copy `fgreport.nas` to `$FG_DATA/Nasal`

__Usage:__

1. Set property `/sim/signals/fgreport` to "1".
2. Wait until the value of property `/sim/signals/fgreport` became empty again.
3. Read the value of property `/sim/fgreport/text`, the content of it is the full text of the report, which can be displayed directly.

---

#### FGTools.sh

A shell script used to control FlightGear via telnet.

Synopsis:

	FGTools.sh report|soundon|soundoff

* __report__  
  Print an report of FlightGear.
* __soundon__  
  Switch the sound of FlightGear on.
* __soundoff__  
  Switch the sound of FlightGear on.

_FG_TELNET_ environment variable should be defined as the telnet address and port of FlightGear, e.g: `FG_TELNET='localhost:5000'`.

---

#### system.fgfsrc

This file contains the FlightGear command line options for:

* An ideal flight environment.
* Telnet configuration for accessing the Property Tree.

Copy it to `$FG_DATA` to activate it.

---

#### 777-300_frank.patch

Patch for [__777-300__](https://code.google.com/p/b773-flightgear/) and [__777-300ER__](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Some modifications to both 777-300 and 777-300ER
* Descending manager for both 777-300 and 777-300ER. For detail, see section __Descending Manager__
* Ground refueling function for 777-300ER, based on the distance of the route.

---

Pause Manager
-------------

Pause the simulation when the remaining distance is shorter than a given value (in nautical miles).

**Install:**

1. Copy `pause-manager.xml` to `$FG_DATA/gui/dialogs`
2. Copy `pause_manager.nas` to `$FG_DATA/Nasal`
3. Add the 'pause-manager' menu item to the menu:


* Edit `$FG_DATA/gui/menubar.xml` to insert  
* Adding this menu item after 'route-manager' menu item is recommened

	
	<item>
		<name>pause-manager</name>
		<label>Pause Manager</label>
		<binding>
			<command>dialog-show</command>
			<dialog-name>pause-manager</dialog-name>
		</binding>
	</item>
	

---

Descending Manager
------------------

When the remaining distance is shorter than 110 nautical miles.

* Set vertical speed to -2000ft/min then switch altitude mode to V/S.

When descend to an altitude near a given value (in feets).

* Switch altitude mode from V/S to FLCH.
* Switch speed brake mode from off to auto.
* Pause simulation and waiting for manual operation.

Installation: This was included in the patch file **777-300_frank.patch**.

---

PHP Interface for FlightGear
----------------------------

#### Description

PHP is used to manipulate the Property Tree of FlightGear via telnet.

At present, screenshot capture and download is implemented.

#### Install

	cp fg-www/* /var/www

`/var/www` is the root directory of your apache server.

#### Filelist

File  | Description
----- | -----------
config.php  | Configuration information
FGTelnet.php  | Telnet tool to access Property Tree of FlightGear.
screenshot.php  | Capture screenshot, then download it or show it within browser.

#### Typical Usage

Capture and download screenshot from command line:

	wget -O screenshot.png localhost/screenshot.php

**PS:** Never add `?>` at the end of PHP files, especially those used for configuration information or PHP library.  
Otherwise, when you're intended to force a file download via PHP, the file to download will be corrupted by unexpected leading empty lines or leading spaces.

