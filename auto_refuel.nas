var auto_refuel_748 = func(range=7600) {
	var route_len = getprop('/autopilot/route-manager/total-distance');
	var fraction = 0.07 + route_len/range;
	fraction = fraction > 1.0 ? 1.0 : fraction;

	for (var i = 0; i < 8; i += 1) {
		setprop('/consumables/fuel/tank[' ~ i ~ ']/level-norm', fraction);
	}
	print('Aircraft refueled.');
}

var auto_refuel_757 = func{
	var range = 3200;
	var route_len = getprop('/autopilot/route-manager/total-distance');
	var fraction = 0.1+route_len/range;
	fraction = fraction > 1.0 ? 1.0 : fraction;
	setprop('/consumables/fuel/tank[0]/level-norm', fraction);
	setprop('/consumables/fuel/tank[1]/level-norm', fraction);
	setprop('/consumables/fuel/tank[2]/level-norm', fraction);
	print('Aircraft refueled.');
}

var do_magic_refuel = func(estimated_range = 7300){
	props.globals.getNode('/command').addChild('magic-refuel');
	props.globals.getNode('/command/magic-refuel').setDoubleValue(0);

	var minimum_amount = 0.10;
	var magic_refuel = func(){
		var target_amount = getprop('/command/magic-refuel');

		#Refuel speed in gal-us/second.
		var refuel_speed = 27;
		var refuel_tanks = 0;
		var tank_count = 0;

		if (target_amount < minimum_amount) {
			props.globals.getNode('/command/magic-refuel').setDoubleValue(0);
			return;
		} else if (getprop('/consumables/fuel/total-fuel-norm') > target_amount
			or getprop('/velocities/vertical-speed-fps') > 5.0
			or getprop('/velocities/vertical-speed-fps') < -5.0) {
			props.globals.getNode('/command/magic-refuel').setDoubleValue(0);
			screen.log.write('Refuel Complete!!!', 0, 1, 0);
			return;
		}

		#Check the count of fuel tanks, maximum 100 tanks
		for (var i = 0; i < 100; i += 1) {
			if (nil == getprop('/consumables/fuel/tank[' ~ i ~ ']/tank-num')) {
				break;
			}
			tank_count += 1;
		}

		#Check how many tanks to refuel
		for (var i = 0; i < tank_count; i += 1) {
			var amount = getprop('/consumables/fuel/tank[' ~ i ~ ']/level-gal_us');
			var capacity = getprop('/consumables/fuel/tank[' ~ i ~ ']/capacity-gal_us');
			if (amount < capacity - 0.1) {
				refuel_tanks += 1;
			}
		}

		#Do refuel
		for (var i = 0; i < tank_count; i += 1) {
			var amount = getprop('/consumables/fuel/tank[' ~ i ~ ']/level-gal_us');
			var capacity = getprop('/consumables/fuel/tank[' ~ i ~ ']/capacity-gal_us');
			amount = amount + (refuel_speed / refuel_tanks);
			if (amount >= capacity) {
				amount = capacity;
			}
			setprop('/consumables/fuel/tank[' ~ i ~ ']/level-gal_us', amount);
			if (!getprop('/consumables/fuel/tank[' ~ i ~ ']/selected')) {
				setprop('/consumables/fuel/tank[' ~ i ~ ']/selected', 1);
			}
		}

		settimer(magic_refuel, 1);
	}
	setlistener('/command/magic-refuel', func{
		if (getprop('/command/magic-refuel') > minimum_amount) {
			magic_refuel();
		}
	});

	var magic_refuel_daemon = func(){
		if (getprop('/autopilot/route-manager/active')
			and getprop('/consumables/fuel/total-fuel-norm') < minimum_amount
			and getprop('/velocities/vertical-speed-fps') < 5.0
			and getprop('/velocities/vertical-speed-fps') > -5.0
			and getprop('/command/magic-refuel') < minimum_amount) {

			var dist_remaining = getprop('/autopilot/route-manager/distance-remaining-nm');
			var target_amount = dist_remaining / estimated_range;
			if (target_amount > 0.99) {
				target_amount = 0.99;
			}
			if (target_amount > minimum_amount) {
				props.globals.getNode('/command/magic-refuel').setDoubleValue(target_amount);
			}

		}
		settimer(magic_refuel_daemon, 1);
	}
	magic_refuel_daemon();
}

var exec_func_list = [
	['747-8i', auto_refuel_748],
	['757-200-PW2040', auto_refuel_757],
	['B-2', do_magic_refuel],
];

_setlistener("/sim/signals/nasal-dir-initialized", func {
	var l_auto_refuel = setlistener('/sim/signals/fdm-initialized', func {
		foreach (var func_data; exec_func_list) {
			if (func_data[0] == getprop('/sim/aircraft')) {
				func_data[1]();
				break;
			}
		}
		removelistener(l_auto_refuel);
	});
});

