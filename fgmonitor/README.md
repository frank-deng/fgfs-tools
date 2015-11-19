fgmonitor
=========

Monitor FlightGear instances from browser.

#### Requirements:

* A browser supports WebGL and [CORS](https://en.wikipedia.org/wiki/Cross-origin_resource_sharing).
* Apache/Nginx, PHP5, PHP5-cURL installed at server side.
* Copy `fgreport.nas` to `$FG_ROOT/Nasal` where FlightGear is deployed.

#### Enable CORS

Nginx:

	add_header 'Access-Control-Allow-Origin' "example.com";

Apache: 

	Header set Access-Control-Allow-Origin example.com

#### Usage

* For PC platform, Press `r` or `space` to update data.
* For mobile platform, shake the device to update data.

