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
	$img_bmp = 'screenshot/scrshot.bmp';
	if (is_file($img_bmp)) {
		unlink($img_bmp);
	}
	if (NULL != $imgpath) {
		exec("convert $imgpath -resize 640x480 -colors 256 bmp3:$img_bmp");
?>
	<p>
		<img src='<?=$imgpath?>'/><br/>
		<a href='<?=$imgpath?>'>Download</a> |
		<a href='<?=$img_bmp?>'>BMP</a>
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

