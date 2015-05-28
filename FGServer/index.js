/* System Information */
function update_sysinfo(){
	$.post('sysinfo.php')
	.success(function(data) {
		$('#cpu_temp').html(data['cpu_temp_c'] + '&deg;C');
		$('#gpu_temp').html(data['gpu_temp_c'] + '&deg;C');
	})
	.error(function(resp){
		$('#cpu_temp').html('N/A');
		$('#gpu_temp').html('N/A');
	});
}

/* FlightGear Report */
fgfs_report = $('.page#fgfs_report').extend({
	update_condition: function(name, condition) {
		if (condition) {
			fgfs_report.find('[condition-true=' + name + ']').show();
			fgfs_report.find('[condition-false=' + name + ']').hide();
		} else {
			fgfs_report.find('[condition-false=' + name + ']').show();
			fgfs_report.find('[condition-true=' + name + ']').hide();
		}
	},
	update_data: function(name, value) {
		fgfs_report.find('[data-bind=' + name + ']').html(value);
	},
	init: function() {
		fgfs_report.find('[data-bind]').html('N/A');
	},
	render_status: function(status) {
		fgfs_report.find('.ajax_status').html(status);
	},
	render_data: function(instance_num, data) {
		if (undefined === data) {
			fgfs_report.find('[data-bind]').html('N/A');
			return;
		}

		_this = fgfs_report;
		_this.update_data('instance_number', instance_num);

		for (var key in data) {
			_this.update_data(key, data[key] == undefined ? 'N/A' : data[key]);
		}
		_this.update_data('lat_lon', data['latitude'].toFixed(6) + ' ' + data['longitude'].toFixed(6));
		_this.update_data('heading', data['heading'].toFixed(1));
		_this.update_data('mach', data['mach'].toFixed(3));
		_this.update_data('vertical_speed', (data['vertical_speed'] * 60).toFixed(0));

		_this.update_data('total_distance', data['total_distance'].toFixed(2));
		_this.update_data('distance_remaining', data['distance_remaining'].toFixed(2));
		_this.update_data('distance_elapsed', (data['total_distance'] - data['distance_remaining']).toFixed(2));

		_this.update_data('fuel_remaining', (data['fuel_remaining'] * 100).toFixed(2) + '%');
		_this.update_data('crashed', data['crashed'] ? 'Yes' : 'No');
		_this.update_data('paused', (data['freezed_clock'] && data['freezed_master']) ? 'Yes' : 'No');

		_this.update_condition('ufo', data['fdm'] == 'ufo');
		_this.update_condition('routemgr_active', data['routemgr_active']);
	},
});
function update_report() {
	var instance_num = fgfs_report.find('.instance_num').val();
	fgfs_report.render_status('Loading...');

	//Fetch FlightGear Report
	$.post(
		'fgfs.php',
		{
			instance: instance_num,
			commands: [
				{command: 'get', prop: '/sim/flight-model', alias: 'fdm'},
				{command: 'get', prop: '/sim/description', alias: 'aircraft'},
				{command: 'get', prop: '/sim/time/real/string', alias: 'real_world_time'},
				{command: 'get', prop: '/sim/time/utc/string', alias: 'utc_time'},
				{command: 'get', prop: '/instrumentation/clock/local-short-string', alias: 'local_time'},
				{command: 'get', prop: '/position/latitude-deg', alias: 'latitude', type: 'float'},
				{command: 'get', prop: '/position/longitude-deg', alias: 'longitude', type: 'float'},
				{command: 'get', prop: '/position/altitude-ft', alias: 'altitude', type: 'int'},
				{command: 'get', prop: '/position/altitude-agl-ft', alias: 'agl', type: 'int'},
				{command: 'get', prop: '/orientation/heading-deg', alias: 'heading', type: 'float'},
				{command: 'get', prop: '/velocities/equivalent-kt', alias: 'equivalent_speed', type: 'int'},
				{command: 'get', prop: '/instrumentation/airspeed-indicator/indicated-speed-kt', alias: 'indicated_air_speed', type: 'int'},
				{command: 'get', prop: '/instrumentation/airspeed-indicator/indicated-mach', alias: 'mach', type: 'float'},
				{command: 'get', prop: '/instrumentation/airspeed-indicator/true-speed-kt', alias: 'air_speed', type: 'int'},
				{command: 'get', prop: '/velocities/groundspeed-kt', alias: 'ground_speed', type: 'int'},
				{command: 'get', prop: '/velocities/vertical-speed-fps', alias: 'vertical_speed', type: 'float'},
				{command: 'get', prop: '/autopilot/route-manager/active', alias: 'routemgr_active', type: 'bool'},
				{command: 'get', prop: '/autopilot/route-manager/total-distance', alias: 'total_distance', type: 'float'},
				{command: 'get', prop: '/autopilot/route-manager/distance-remaining-nm', alias: 'distance_remaining', type: 'float'},
				{command: 'get', prop: '/autopilot/route-manager/flight-time-string', alias: 'flight_time'},
				{command: 'get', prop: '/autopilot/route-manager/ete-string', alias: 'time_remaining'},
				{command: 'get', prop: '/consumables/fuel/total-fuel-norm', alias: 'fuel_remaining', type: 'float'},
				{command: 'get', prop: '/sim/crashed', alias: 'crashed', type: 'bool'},
				{command: 'get', prop: '/sim/freeze/clock', alias: 'freezed_clock', type: 'bool'},
				{command: 'get', prop: '/sim/freeze/master', alias: 'freezed_master', type: 'bool'},
			]
		}
	)
	.success(function(data) {
		fgfs_report.render_status('Ok.');
		fgfs_report.render_data(instance_num, data);
	})
	.error(function(resp){
		fgfs_report.render_status('Instance Not Available.');
		fgfs_report.render_data(instance_num, undefined);
	});
};

/* FlightGear Workbench */
fgfs_workbench = $('.page#fgfs_workbench').extend({
	init: function() {
		fgfs_workbench.render_loading();
		fgfs_workbench.find('#fps_limit_enabled').change(function(e){
			if (e.target.checked) {
				fgfs_workbench.find('#fps_limit').removeAttr('disabled');
			} else {
				fgfs_workbench.find('#fps_limit').attr('disabled', 'disabled');
			}
		});
		fgfs_workbench.find('#pausemgr_enabled').change(function(e){
			if (e.target.checked) {
				fgfs_workbench.find('#pausemgr_dist').removeAttr('disabled');
			} else {
				fgfs_workbench.find('#pausemgr_dist').attr('disabled', 'disabled');
			}
		});
	},
	render_status: function(_status) {
		fgfs_workbench.find('.ajax_status').html(_status);
	},
	render_loading: function() {
		fgfs_workbench.find('input,#btn_apply,#btn_shutdown').attr('disabled', 'disabled');
		fgfs_workbench.find('#btn_refresh').removeAttr('disabled');
	},
	render_error: function(instance_num, resp) {
		fgfs_workbench.find('input,#btn_apply,#btn_shutdown').attr('disabled', 'disabled');
		fgfs_workbench.find('#btn_refresh').removeAttr('disabled');
		fgfs_workbench.find('input[type=checkbox]').prop('checked', false);
		fgfs_workbench.find('input[type=text]').val('');
	},
	render_data: function(instance_num, data) {
		fgfs_workbench.find('input, #btn_apply, #btn_shutdown').removeAttr('disabled');
		var _this = fgfs_workbench;

		var fps_limit = data['fps_limit'];
		if (fps_limit > 0) {
			_this.find('#fps_limit_enabled').prop('checked', true);
			_this.find('#fps_limit').removeAttr('disabled');
			_this.find('#fps_limit').val(fps_limit);
		} else {
			_this.find('#fps_limit_enabled').prop('checked', false);
			_this.find('#fps_limit').attr('disabled', 'disabled');
			_this.find('#fps_limit').val(fps_limit);
		}

		if (data['pausemgr_dist'] > 0) {
			_this.find('#pausemgr_enabled').prop('checked', true);
			_this.find('#pausemgr_dist').removeAttr('disabled');
			_this.find('#pausemgr_dist').val(data['pausemgr_dist']);
		} else {
			_this.find('#pausemgr_enabled').prop('checked', false);
			_this.find('#pausemgr_dist').attr('disabled', 'disabled');
		}

		_this.find('#sound_enabled').prop('checked', data['sound_enabled']);
		_this.find('#pause_sim').prop('checked', data['freezed_clock'] && data['freezed_master']);
	},
	fetch_data: function() {
		var _this = fgfs_workbench;
		var commands = [];

		var fps_limit_enabled = _this.find('#fps_limit_enabled').prop('checked');
		commands.push({command : 'set', prop: '/sim/gui/frame-rate-throttled', value: (fps_limit_enabled ? '1' : '0')});
		if (fps_limit_enabled) {
			commands.push({command : 'set', prop: '/sim/frame-rate-throttle-hz', value: _this.find('#fps_limit').val()});
		} else {
			commands.push({command : 'set', prop: '/sim/frame-rate-throttle-hz', value: '0'});
		}

		var pausemgr_dist = 0;
		if (_this.find('#pausemgr_enabled').prop('checked')) {
			pausemgr_dist = _this.find('#pausemgr_dist').val();
		} else {
			pausemgr_dist = -1;
		}
		commands.push({command : 'set', prop: '/autopilot/pausemgr-dist', value: pausemgr_dist});

		commands.push({command : 'set', prop: '/sim/sound/enabled', value: (_this.find('#sound_enabled').prop('checked') ? 'true' : 'false')});
		commands.push({command : 'set', prop: '/sim/freeze/clock', value: (_this.find('#pause_sim').prop('checked') ? 'true' : 'false')});
		commands.push({command : 'set', prop: '/sim/freeze/master', value: (_this.find('#pause_sim').prop('checked') ? 'true' : 'false')});

		//Validate data
		var fps_limit = _this.find('#fps_limit').val();
		if (fps_limit_enabled && (isNaN(fps_limit) || fps_limit < 15 || fps_limit > 70)) {
			window.alert('Invalid value for FPS Limit.');
			return undefined;
		}
		if (isNaN(_this.find('#pausemgr_dist').val())){
			window.alert('Invalid value for Pause Manager Distance.');
			return undefined;
		}

		//Return data
		return commands;
	},
});
function update_workbench() {
	var instance_num = fgfs_workbench.find('.instance_num').val();

	//Preparation before sending request
	fgfs_workbench.render_status('Loading...');
	fgfs_workbench.render_loading();

	//Send request
	$.post(
		'fgfs.php',
		{
			instance: instance_num,
			commands: [
				{command: 'get', prop: '/sim/frame-rate-throttle-hz', alias: 'fps_limit', type: 'int'},
				{command: 'get', prop: '/autopilot/pausemgr-dist', alias: 'pausemgr_dist', type: 'float'},
				{command: 'get', prop: '/sim/freeze/clock', alias: 'freezed_clock', type: 'bool'},
				{command: 'get', prop: '/sim/freeze/master', alias: 'freezed_master', type: 'bool'},
				{command: 'get', prop: '/sim/sound/enabled', alias: 'sound_enabled', type: 'bool'},
			]
		}
	)
	.success(function(data) {
		fgfs_workbench.render_status('Ok.');
		fgfs_workbench.render_data(instance_num, data);
	})
	.error(function(resp){
		fgfs_workbench.render_status('Instance Not Available.');
		fgfs_workbench.render_error(instance_num, resp);
	});
}
function apply_change() {
	var instance_num = fgfs_workbench.find('.instance_num').val();
	var data = fgfs_workbench.fetch_data();
	if (undefined === data) {
		return;
	}

	$.post(
		'fgfs.php',
		{
			instance: instance_num,
			commands: data
		}
	)
	.error(function(resp){
		window.alert('Failed to apply changes.');
	});
}
function shutdown_sim() {
	var instance_num = fgfs_workbench.find('.instance_num').val();

	if (!window.confirm('There is no back after shutdown selected FlightGear instance.\nProcess Anyway?')) {
		return;
	}
	$.post(
		'fgfs.php',
		{instance: instance_num, commands: [{command: 'run', param: 'exit'}]},
		function() {
			update_workbench();
		}
	).error(function(){
		window.alert('Failed to shutdown FlightGear.');
	});
}

/* Common */
$('.instance_num').change(function(e){
	$('.instance_num').val(e.target.value);
});
function show_page(page_id) {
	$('.page').hide();
	$('.page#' + page_id).show().find('#btn_refresh').trigger('click');
}
$(window).load(function(){
	$('.page#sys_info #btn_refresh').trigger('click');
	fgfs_report.init();
	fgfs_workbench.init();
});

