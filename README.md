fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### FGTelnet.py

Python library for interacting with FlightGear props telnet interface.

---

#### fgtools fgtools.nas

A useful tool to manipulate FlightGear from command line.

Synopsis: `fgtools instance_num command [parameter] ...`

`instance_num` is used to determine which FlightGear instance should be launched or accessed, specified by one of 0-9. At most 10 instances could be launched at one time.

Multiple digits for `instance_num` means executing command for each instance specified, one digit for one instance, except **launch** command which only applies to the first instance specified.

Available Commands:

`fpslimit [FPS]`:

* If FPS is not given, show maximum FPS.
* If FPS is given, maximum FPS will be set as the given FPS value.
* The value of FPS can be 0 or 15-70.

`launch [-f FLIGHT_PLAN] AIRCRAFT [fgfs_options]`:

Start FlightGear with the aircraft specified.

If FLIGHT_PLAN is specified,

* The aircraft will be placed at the departure airport & runway specified by the Flight Plan.
* ILS frequency will be automatically prepared for the distination runway.
* The simulation will be paused when the remaining route is short than 20 nmi  
  You can append command `--prop:/autopilot/pausemgr-dist=-1` in `fgfs_options` to deactivate Pause Manager.

`loadroute FLIGHT_PLAN`:

Load flight plan from file specified by FLIGHT_PLAN.

`pause/resume`

Pause/Resume simulation.

`pausemgr [DISTANCE]`:

* If DISTANCE is not given, show the state of Pause Manager.
* If DISTANCE is given as a positive number, pausemgr will be activated with the given DISTANCE.
* If DISTANCE is given as a negative number, pausemgr will be deactivated.

`report`

Print an report of FlightGear.

`shutdown`

Shutdown simulation.

`soundon/soundoff`

Unmute/Mute FlightGear.

Please copy `fgtools.nas` to `$FG_ROOT/Nasal` first, so as to get all the stuffs here work well.

---

#### Pause Manager

Automatically pause the simulation when the remaining route is shorter than a given distance or the airplane crashed.

This feature is included in `fgtools.nas`.

Usage:

Set property `/autopilot/pausemgr-dist` with a positive value will activate Pause Manager. A negative value will deactive Pause Manager.

When the remaining route is shorter than the distance specified by property `/autopilot/pausemgr-dist` or the airplane crashed:

* The simulation will be paused.
* FPS will be reduced to 10fps before the simulation resumed. Once the simulation resumed, the FPS will be set to 60fps.
* Property `/autopilot/pausemgr-dist` will be set to **-1**, which means the Pause Manager is inactive.

You can change the value of property `/autopilot/pausemgr-dist` at any time to adjust the distance or activate/deactivate Pause Manager.

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

#### Misceallanous

Directory __Around_The_World__ contains a series of flight plans based on Jules Verne's novel _Around the World in Eighty Days_. While the whole journey takes only 8 days by Boeing 757.

Directory __routes__ contains flight plans for long-haul and ultra long-haul flights designed for Boeing 777-300ER and Boeing 747-8i.

