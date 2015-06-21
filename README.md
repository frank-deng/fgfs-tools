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

`launch [-f FLIGHT_PLAN [-p PAUSE_DISTANCE_NMI]] Aircraft [fgfs_options] ...`:

Start FlightGear with the aircraft specified.

If FLIGHT_PLAN is specified,

* The aircraft will be placed at the departure airport & runway specified by the Flight Plan.
* ILS frequency will be automatically prepared for the distination runway.
* The simulation will be paused when the remaining route is short than 20nmi  
  You can specify `PAUSE_DISTANCE_NMI` to set a distance other than 20nmi for Pause Manager.

`pause` `resume`

Pause/Resume simulation.

`report`

Print an report of FlightGear.

`shutdown`

Shutdown simulation.

`soundon` `soundoff`

Unmute/Mute FlightGear.

Please copy `fgtools.nas` to `$FG_ROOT/Nasal` first, so as to get all the stuffs here work well.

---

#### temperature

A utility to display both CPU and GPU's temperature. `nvidia-smi` is used for getting GPU temperature at present.

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

---

#### B-2.patch

Patch for [Northrop B-2 Spirit](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.4/B-2_20140909.zip):

* Fixed the missing engine sound.
* Fixed the misplaced hotspots.
* Changed the vertical speed under Altitude Hold mode.

---

#### Misceallanous

Directory __Around_The_World__ contains a series of flight plans based on Jules Verne's novel _Around the World in Eighty Days_. While the whole journey takes only 8 days by Boeing 757.

Directory __routes__ contains flight plans for long-haul and ultra long-haul flights.

