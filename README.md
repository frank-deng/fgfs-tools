fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### fgtools

A useful tool to manipulate FlightGear from command line.

Synopsis: `fgtools command`

Available Commands:

* report  
  Print an report of FlightGear.
* soundon  
  Unmute FlightGear.
* soundoff  
  Mute FlightGear.
* pause  
  Pause simulation.
* resume  
  Resume simulation.
* pausemgr [distance]  
  View or setup the state of Pause Manager.  
  If distance is not given, the state of Pause Manager will be shown.  
  If distance is a positive number, Pause Manager will be activated with the given distance.  
  If distance is a negative number, Pause Manager will be deactivated.  

`FG_TELNET` environment variable should be defined as the telnet address:port for FlightGear, e.g: `FG_TELNET='localhost:5401'`.

---

### 777-tools

A useful tool to manipulate 777-300ER from command line.

Synopsis: `777-tools command`

Available Commands:

* getnavfreq  
  Get ILS frequency.
* setnavfreq  
  Set ILS frequency.
* loadroute ROUTE_FILE  
  Load flight plan from ROUTE_FILE.
* refuel [percent]  
  Refuel 777-300ER.  
  If percent is given, then the quantity of fuel will be percent% of total fuel capacity. Otherwise, the quantity of fuel is determined by the total distance of the route.
* descending [altitude]
  View or setup the state of Descending Manager.  
  If altitude is not given, the state of Descending Manager will be shown.  
  If altitude is a positive number, Descending Manager will be activated with the given target altitude.  
  If altitude is a negative number, Descending Manager will be deactivated.  

---

#### fgreport.nas

A FlightGear Nasal script used to generate report.

Install: copy `fgreport.nas` to `$FG_ROOT/Nasal`

Usage:

1. Set property `/sim/signals/fgreport` to "1".
2. Wait until the value of property `/sim/signals/fgreport` became empty again.
3. Read the value of property `/sim/fgreport/text`, the content of it is the full text of the report, which can be displayed directly.

---

#### system.fgfsrc

This file contains the FlightGear command line options for:

* An ideal flight environment.
* Telnet configuration for accessing the Property Tree.

Copy it to `$FG_ROOT` to activate it.

---

#### 777-300_frank.patch

Patch for [777-300](https://code.google.com/p/b773-flightgear/) and [777-300ER](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Some modifications to both 777-300 and 777-300ER
* Descending manager for both 777-300 and 777-300ER. For detail, see section **Descending Manager**.
* Ground refueling function for 777-300ER, based on the distance of the route.

---

Pause Manager
-------------

Pause the simulation when the remaining distance is shorter than a given value (in nautical miles).

Install:

1. Copy `pause-manager.xml` to `$FG_ROOT/gui/dialogs`
2. Copy `pause_manager.nas` to `$FG_ROOT/Nasal`
3. Add the 'pause-manager' menu item to the menu:


* Edit `$FG_ROOT/gui/menubar.xml` to insert  
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

