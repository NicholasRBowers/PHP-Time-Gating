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
		<h3><?php
		$details = getGateDetails();
    if ($details[0]) {
      echo 'Come on in!  We just opened at '.date('G:i m/d', $details[1]).', and we close at '.date('G:i m/d/Y', $details[2]).'.';
    } else {
      echo 'We\'re sorry.  We closed at '.date('g:ia', $details[1]).'.  We\'ll be open again at '.date('g:ia', $details[2]).'.';
    }?></h3>
	</body>
</html>