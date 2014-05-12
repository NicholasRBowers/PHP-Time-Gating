<!DOCTYPE html>
<?php include('time-gate.php'); ?>
<html>
	<head>
		<meta charset="utf-8">
		<title>PHP Time Gate</title>
		<style type="text/css">
			body {
				font-family: 'Helvetica Neue', arial;
				text-align: center;
			}
		</style>
	</head>

	<body>
		<h1>Gadgets Inc.</h1>
		<h2>Store Status</h2>
		<h3><?php if(isOpenNow()) {echo 'Open.';} else {echo 'Closed.';} ?></h3>
	</body>
</html>