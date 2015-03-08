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
* loadroute FLIGHT_PLAN  
  Load flight plan from file.
* launch AIRCRAFT FLIGHT_PLAN  
  Start FlightGear with aircraft specified, start at airport & runway provided by the departure info of flight plan.

`FG_TELNET` environment variable should be defined as the telnet address:port for FlightGear, e.g: `FG_TELNET='localhost:5401'`.

---

### B-1B-tools

A useful tool to manipulate B-1B from command line.

Synopsis: `B-1B-tools command`

Available Commands:

* refuel  
  Refuel B-1B with full fuel on the ground.
* mrefuel [amount]  
  See section *Magic Refuel* for detail.
* mrefueld [off|REFUEL_POINTS]  
  See section *Magic Refuel Daemon* for detail.

---

#### fgreport.nas

A FlightGear Nasal script used to generate report.

Install: copy `fgreport.nas` to `$FG_ROOT/Nasal`

Usage:

1. Set property `/sim/signals/fgreport` to "1".
2. Wait until the value of property `/sim/signals/fgreport` became empty again.
3. Read the value of property `/sim/fgreport/text`, the content of it is the full text of the report, which can be displayed directly.

---

#### setILSFreq.nas

Setup ILS Frequency for the destination airport and runway automatically when a flight plan was activated.

If `--flight-plan=file` argument is specified on the `fgfs` command line, the flight plan will be automatically activated and ILS frequency will also prepared for the destination airport and runway.

---

#### system.fgfsrc

This file contains the FlightGear command line options for:

* An ideal flight environment.
* Telnet configuration for accessing the Property Tree.

Copy it to `$FG_ROOT` to activate it.

---

#### 777-300.patch

Patch for [777-300](https://code.google.com/p/b773-flightgear/) and [777-300ER](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Adjusted the vertical speed in FLCH mode.
* Ground refueling function for 777-300ER, based on the distance of the route.
* Adjusted Autopilot System so as to fly polar route.
* Reduced the time of manual startup.

---

#### 757-200.patch

Patch for 757-200.

* Adjusted the vertical speed in FLCH mode.
* Ground refueling function for 757-200, based on the distance of the route.

---

#### 747-8i.patch

Patch for [747-8i](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip)

* Adjusted the vertical speed in FLCH mode.
* Calculate the amount of fuel based on the total distance of the route.

---

#### B-1B.patch

Patch for [Rockwell B-1B Lancer](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.2/B-1B_20130823.zip):

* Changed the default target speed from 350 to 300.
* Add the feature of *Magic Refuel*, see section *Magic Refuel* for detail.

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

Magic Refuel
------------

Perform an aerial refuel for B-1B Lancer, but without the presense of tanker, and the aircraft's speed and altitude will not changed. Which will make you free from all the difficulties of performing a real aeiral refueling.

Usage:

1. Set property `/armament/magic-refuel/amount` to the amount you'd like to refuel to.
2. Set property `/armament/magic-refuel/signal` to `1` to start refueling.
3. You can set `/armament/magic-refuel/signal` to `0` to stop refueling.

---

Magic Refuel Daemon
-------------------

Automatically activate _Magic Refuel_ for B-1B Lancer when it is going to reach a specific waypoint, which is called Refuel Point.

Usage:

1. Set property `/armament/magic-refuel/refuel-points` to specify the Refuel Points.  
   Format: `WP1:AMOUNT1,WP2:AMOUNT2,...`, e.g: `YSSY:0.97,KEDW:0.85`  
   Argument REFUEL_POINTS shares the same format as mentioned here.
2. Set property `/armament/magic-refuel/daemon` to `1` to activate Magic Refuel Daemon.
3. Set property `/armament/magic-refuel/daemon` to `0` to deactivate Magic Refuel Daemon.

