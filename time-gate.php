<?php
require_once('time-gate-hours.php');

function isClosedAllDay($date, $openHours, $exceptions) { // Check if gate is closed on a specific day.
	// Prepare variables.
	$date = strtotime($date);
	$day = strtolower(date("D", strtotime($date)));
	
	// Check if today is an exception.
	foreach($exceptions as $ex => $ex_day) {
		$ex_day = strtotime($ex_day);
		if ($ex_day === $date) {
			return true;
		}
	}

	// Check if the store is closed all day.
	if ($openHours[$day][0] == '00:00-00:00') {
		return true;
	}
	return false;
}

function isOpen($time, $date, $openHours, $exceptions) { // Check if the gate is open during a specific time.
  // Prepare variables.
  $time = strtotime($time);
  $day = strtolower(date("D", strtotime($date)));

  // Check if the gate is closed all day.
  if (isClosedAllDay($date, $openHours, $exceptions)) {
  	return false;
  }

  // Check if the gate is open.
  foreach($openHours[$day] as $range) {
  	$range = explode("-", $range);
  	$start = strtotime($range[0]);
  	$end = strtotime($range[1]);
  	if (($start <= $time) && ($time <= $end)) {
  		return true;
  	}
  }
  return false;
}

function isOpenNow($openHours, $exceptions) { // Check if the gate is open now.
	$time = date("G:i");
	$date = date("m/d/Y");

	return isOpen($time, $date, $openHours, $exceptions);
}

?>