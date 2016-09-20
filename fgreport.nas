_setlistener("/sim/signals/nasal-dir-initialized", func {
    setprop('/fgreport/aircraft', getprop('/sim/description'));
    settimer(func{
        setprop('/fgreport/latitude-deg', getprop('/position/latitude-deg'));
        setprop('/fgreport/longitude-deg', getprop('/position/longitude-deg'));
        setprop('/fgreport/ete-string', getprop('/autopilot/route-manager/ete-string'));
        setprop('/fgreport/flight-time-string', getprop('/autopilot/route-manager/flight-time-string'));
        setprop('/fgreport/distatnce-remaining-nm', getprop('/autopilot/route-manager/distatnce-remaining-nm'));
        setprop('/fgreport/total-distance', getprop('/autopilot/route-manager/total-distance'));
    }, 0.1);
});