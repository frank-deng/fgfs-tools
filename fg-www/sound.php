<?php
require('FGTools.php');
require('config.php');
$fg = NULL;
try {
	$fg = new FGTelnet(FG_HOST, FG_PORT);
	if (isset($_GET['enabled'])) {
		$fg->set('/sim/sound/enabled', $_GET['enabled']);
	}
	header('Location: index.php');
} catch (Exception $e) {
	header(sprintf('Location: fail.php?message=%s', urlencode($e->getMessage())));
	exit(1);
}
?>
