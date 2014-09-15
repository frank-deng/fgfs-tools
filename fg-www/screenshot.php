<?php
require('FGTools.php');
require('config.php');

$fg = NULL;
try {
	$fg = new FGTelnet(FG_HOST, FG_PORT);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'/>
		<title>FlightGear Screenshot</title>
	</head>
	<body>
		<h1>Screenshot</h1>
		<p>| <a href='screenshot.php?view=prev'>Prev View</a>
| <a href='screenshot.php?view=next'>Next View</a>
|</p>
<?php
	/* Handle view switching */
	if (isset($_GET['view'])) {
		switch ($_GET['view']) {
			case 'prev':
				$fg->set('/command/view/prev', '1');
			break;
			case 'next':
				$fg->set('/command/view/next', '1');
			break;
		}
	}

	/* Take screenshot */
	$imgpath = screenshot($fg);
	if (NULL != $imgpath) {
?>
	<p><img src='<?=$imgpath?>'/><br/><a href='<?=$imgpath?>'>Download</a></p>
<?php
	} else {
		echo '<p>Failed to capture screenshot.</p>'."\n";
	}
?>
		<p><a href='index.php'>Back</a></p>
	</body>
</html>
<?php
} catch (Exception $e) {
	header(sprintf('Location: fail.php?message=%s', urlencode($e->getMessage())));
	exit(1);
}
?>

