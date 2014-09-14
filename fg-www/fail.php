<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'/>
		<title>FlightGear</title>
	</head>
	<body>
		<h1>FlightGear Error</h1>
		<p><?=date('r')?></p>
<?php
if (isset($_GET['message'])) {
	echo '<p>'.urldecode($_GET['message']).'</p>';
}
?>
	</body>
</html>

