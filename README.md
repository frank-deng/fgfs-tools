fgfs-tools
==========

A collection of useful tools, patches, routes, etc for FlightGear.

---

#### fglaunch

Start FlightGear with the aircraft specified.

Synopsis: `fglaunch [-f FLIGHT_PLAN [-p PAUSE_DISTANCE_NMI]] Aircraft [fgfs_options] ...`:

If FLIGHT_PLAN is specified,

* The aircraft will be placed at the departure airport & runway specified by the Flight Plan.
* ILS frequency will be automatically prepared for the distination runway.
* The simulation will be paused when the remaining route is short than 20nmi  
  You can specify `PAUSE_DISTANCE_NMI` to set a distance other than 20nmi for Pause Manager.

---

#### route2kml

Convert FlightGear route to KML format, so as to display the route in [Marble](http://marble.kde.org).

Synopsis: `route2kml [INPUT_FILE] [OUTPUT_FILE]`:

* If INPUT_FILE is not specified, then stdin is used.
* If OUTPUT_FILE is not specified, then stdout is used.

---

#### 777-300.patch

Patch for [777-300](https://code.google.com/p/b773-flightgear/) and [777-300ER](https://code.google.com/p/b773-flightgear/):

* Some new viewports added to 777-300ER
* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 777-300ER on startup based on the distance of the route if flight plan is specified on startup.
* Adjusted Autopilot System so as to fly polar route.
* Reduced the time of manual startup.

---

#### 757-200.patch

Patch for [757-200](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/757-200_20150111.zip).

* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 757-200 on startup based on the distance of the route if flight plan is specified on startup.

---

#### 747-8.patch

Patch for [747-8f](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip) and [747-8i](http://mirrors.ibiblio.org/pub/mirrors/flightgear/ftp/Aircraft-3.4/747-8i_20150111.zip)

* Adjusted the vertical speed in FLCH mode.
* Automatically refuel 747-8f and 747-8i on startup based on the distance of the route if flight plan is specified on startup.

---

#### AN-225-Mrija.patch

Patch for [AN-225-Mrija](https://github.com/HerbyW/AN-225-Mrija):

* Added automatic ground refuel feature, which can calculate out the fuel needed based on the route and aircraft's payload.
* Adjusted autopilot.

---

#### B-1B.patch

Patch for [Rockwell B-1B Lancer](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.2/B-1B_20130823.zip):

* Changed the default target speed from 350 to 300.
* Changed the vertical speed under Altitude Hold mode.

---

#### B-2.patch

Patch for [Northrop B-2 Spirit](ftp://ftp.de.flightgear.org/pub/fgfs/Aircraft-3.4/B-2_20140909.zip):

* Fixed the missing engine sound.
* Fixed the misplaced hotspots for selecting pilot mode.
* Changed the vertical speed under Altitude Hold mode.

---

#### fgtools.nas

This Nasal script includes the following features:

* Pause Manager
* Automatically activate Route Manager and ILS Frequency
* Convert ETE time, Flight time in seconds to HH:MM:SS format;

Copy this file to `$FG_ROOT/Nasal` to make it work.

---

#### gps.xml

Added the folling information:

* Full UTC Time
* Local Time
* Total Distance
* Distance Remaining
* Flight Time
* ETE

To make the additional information work in `gps.xml`, please make sure that `fgtools.nas` exists under `$FG_ROOT/Nasal`.

Copy this file to `$FG_ROOT/gui/dialogs` to replace the original `gps.xml`.

---

#### Misceallanous

Directory __routes__ contains flight plans for long-haul and ultra long-haul flights, as well as flight plans based on Jules Verne's novel _Around the World in Eighty Days_, which takes only 8 days to finish by Boeing 757.

