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
<?php
	/* Take screenshot */
	$imgpath = screenshot($fg);
	$imgcompact = preg_replace('/\.png$/i', '.bmp', $imgpath);
	exec("convert $imgpath -resize 640x480 -colors 256 bmp3:$imgcompact");
	if (NULL != $imgpath) {
?>
	<p>
		<img src='<?=$imgpath?>'/><br/>
		<a href='<?=$imgpath?>'>Download</a> |
		<a href='<?=$imgcompact?>'>Download Compact Version</a>
	</p>
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

