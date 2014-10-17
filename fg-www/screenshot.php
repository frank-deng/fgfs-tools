<?php
require('FGTools.php');
require('config.php');

$fg = NULL;
try {
	$fg = new FGTelnet(FG_TELNET_HOST, FG_TELNET_PORT);

	/* Take screenshot */
	$type='image/png';
	$imgpath = screenshot($fg);
	$img_bmp = 'screenshot/scrshot.bmp';
	if (is_file($img_bmp)) {
		unlink($img_bmp);
	}

	/* Check image */
	if (NULL == $imgpath || !file_exists($imgpath)) {
		throw new Exception('Failed to capture screenshot.');
		exit(1);
	}

	/* Convert image to Windows 3.x compatible bmp */
	if (isset($_GET['win3x'])) {
		exec("convert $imgpath -resize 640x480 -colors 256 -depth 8 bmp2:$img_bmp");
		$imgpath = $img_bmp;
		$type='image/bmp';
	}

	/*Start downloading*/
	Header("Content-type: ".$type);
	$image= file_get_contents($imgpath);
	echo $image;
} catch (Exception $e) {
	echo $e->getMessage();
	exit(1);
}
