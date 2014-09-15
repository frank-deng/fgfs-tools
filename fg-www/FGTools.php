<?php
require('FGTelnet.php');

class DateInterval2 extends DateInterval {
    public function recalculate() {
        $from = new DateTime;
        $to = clone $from;
        $to = $to->add($this);
        $diff = $from->diff($to);
        foreach ($diff as $k => $v) $this->$k = $v;
        return $this;
    }
}
function bool($str) {
	if ('TRUE' == strtoupper($str)) {
		return TRUE;
	} else if ((int)($str) > 0) {
		return TRUE;
	} else {
		return FALSE;
	}
}
function ispaused($fg) {
	if (bool($fg->get('/sim/freeze/clock')) && bool($fg->get('/sim/freeze/master'))) {
		return TRUE;
	} else {
		return FALSE;
	}
}
function screenshot($fg) {
	//Remove last screenshot
	$last_screenshot = $fg->get('/sim/paths/screenshot-last');
	if (is_file($last_screenshot)) {
		unlink($last_screenshot);
	}

	//Do capture screenshot
	if (strcmp('<completed>', $fg->run('screen-capture'))) {
		return NULL;
	}

	//Waiting for screenshot finishes
	if (bool($fg->get('/sim/freeze/clock'))) {
		sleep(2);
	} else {
		while (bool($fg->get('/sim/freeze/master'))) {
			usleep(500000);
		}
	}

	//Finish
	$screenshot = $fg->get('/sim/paths/screenshot-last');
	$screenshot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $screenshot);
	return $screenshot;
}
?>

