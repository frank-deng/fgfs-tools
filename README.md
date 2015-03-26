fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### fgtools
#### fgtools.nas

A useful tool to manipulate FlightGear from command line.

Synopsis: `fgtools instance_num command [parameter] ...`

`instance_num` is used to determine which FlightGear instance should be launched or accessed, specified by one of 0-9. At most 10 instances could be launched at one time.

Multiple digits for `instance_num` means executing command for each instance specified, one digit for one instance.

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
* fpslimit FPS  
  Set maximum FPS to reduce CPU/GPU load.
* pausemgr [distance]  
  View or setup the state of Pause Manager.  
  If distance is not given, the state of Pause Manager will be shown.  
  If distance is a positive number, Pause Manager will pause the simulation when the remaining distance is short than the given distance.  
  If distance is a negative number, Pause Manager will be deactivated.  
* launch AIRCRAFT FLIGHT_PLAN [fgfs_options]  
  Start FlightGear with aircraft specified, start at airport & runway provided by the departure info of flight plan. ILS frequency will be automatically prepared for the distination runway.

Please copy `fgtools.nas` to `$FG_ROOT/Nasal` first, so as to get all the stuffs here work well.

---

#### 777-300.patch

Patch for [777-300](https://code.google.com/p/b773-flightgear/) and [777-300ER](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 777-300ER on startup based on the distance of the route if launched via `fgtools launch` and flight plan is specified.
* Adjusted Autopilot System so as to fly polar route.
* Reduced the time of manual startup.

---

#### 757-200.patch

Patch for [757-200](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/757-200_20150111.zip).

* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 757-200 on startup based on the distance of the route if launched via `fgtools launch` and flight plan is specified.

---

#### 747-8.patch

Patch for [747-8f](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip) and [747-8i](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip)

* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 747-8f and 747-8i on startup based on the distance of the route if launched via `fgtools launch` and flight plan is specified.

---

#### B-1B.patch

Patch for [Rockwell B-1B Lancer](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.2/B-1B_20130823.zip):

* Changed the default target speed from 350 to 300.
* Add the feature of *Magic Refuel*, see section *Magic Refuel* for detail.

#### Magic Refuel

Perform an aerial refuel for B-1B Lancer, but without the presense of tanker, and the aircraft's speed and altitude will not changed. Which will make you free from all the difficulties of performing a real aeiral refueling.

Usage:

1. Set property `/armament/magic-refuel/amount` to the amount you'd like to refuel to.
2. Set property `/armament/magic-refuel/signal` to `1` to start refueling.
3. You can set `/armament/magic-refuel/signal` to `0` to stop refueling.

#### Magic Refuel Daemon

Automatically activate _Magic Refuel_ for B-1B Lancer when it is going to reach a specific waypoint, which is called Refuel Point.

To define a Refuel Point, you should edit the flight plan XML file directly. Find out the waypoint you'd like to mark as a Refuel Point, then append `-REFUEL` after the waypoint ID.

---

