<?php
require_once('time-gate-hours.php');
$now = date("G:i");
$today = date("m/d");

function isOpen($time = $now, $date = $today, $openHours = $openHours, $exceptions = $exceptions) {
  // Prepare variables.
  $time = strtotime($time);
  $day = strtolower(date("D", strtotime($date)));

  // Check if today is an exception or if the gate is normally closed all day.
  if (isException($date, $exceptions) || isNormallyClosedAllDay($date, $openHours)) {
    return false;
  } elseif (isNormallyOpenAllDay($date, $openHours)) { // Check if the gate is normally open all day.
    return true;
  }

  // Check if the gate is open.
	foreach($openHours[$day] as $range) {
		$range = explode("-", $range);
		$start = strtotime($range[0]);
		$end = strtotime($range[1]);

    // If the gate is open,
		if (($start <= $time) && ($time <= $end)) {
      return true;
		}
	}
	return false;
}

function isNormallyOpenAllDay($date = $today, $openHours = $openHours) {
  // Prepare variables.
  $date = strtotime($date);
  $day = strtolower(date("D", strtotime($date)));

  // Check if the store is open all day.
  if($openHours[$day][0] === '00:00-00:00') {
    return true;
  } else {
    return false;
  }
}

function isNormallyClosedAllDay($date = $today, $openHours = $openHours) {
	// Prepare variables.
  $date = strtotime($date);
	$day = strtolower(date("D", strtotime($date)));

  // Check if the store is closed all day.
	if (count($openHours[$day]) === 0 || $openHours[$day][0] == '') {
		return true;
	} else {
    return false;
  }
}

function isException($date = $today, $exceptions = $exceptions) {
  // Prepare variables.
  $date = strtotime($date);

  // Check if today is an exception.
  foreach($exceptions as $ex => $exDay) {
    $exDay = strtotime($exDay);
    if ($exDay === $date) {
      return true;
    }
  }
  return false;
}

?>