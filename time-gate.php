<?php
require_once('time-gate-hours.php');

// isOpen(string $time, string $date, array $openHours [, boolean $getDetails [, array $exceptions]])
// $time is the time for which you would like to know the status of the gate.
// $date is the date for which you would like to know the status of the gate.
// $openHours is the array in which the normal gate hours are stored.
// $getDetails (default = false) tells the function if you would like to get the bounds of the current status of the gate,
	// i.e. If the gate is open, get the time it opened, and the time it will be closing;
	// If the gate is closed, get the time it closed, and the time it will be opening.
// $exceptions (optional) is the array in which the exceptions (closed all day) to the normal $openHours are stored.
function isOpen($time, $date, $openHours, $getDetails = false, $exceptions) { // Check if the gate is open during a specific time.
  $time = strtotime($time); 																									// Prepare variables.
  $day = strtolower(date("D", strtotime($date)));

  if (isClosedAllDay($date, $openHours, $exceptions)) { 											// Check if the gate is closed all day.
  	return false;
  }

  if (count($openHours[$day]) === 0) { 																				// Check if the gate is open all day.
  	return true;
  } else {
  	foreach($openHours[$day] as $range) { 																		// Check if the gate is open.
  		$range = explode("-", $range);
  		$start = strtotime($range[0]);
  		$end = strtotime($range[1]);
  		if (($start <= $time) && ($time <= $end)) {
  			if ($getDetails) {
  				// What about the case when you're open past midnight?
  				return [true, $start, $end];
  			} else {
  				return true;
  			}
  		}
  	}
  	if ($getDetails) {
  		return [false, '', ''];
  	} else {
  		return false;
  	}
  }
}

function isClosedAllDay($date, $openHours, $exceptions) { // Check if gate is closed on a specific day.
	$date = strtotime($date); 															// Prepare variables.
	$day = strtolower(date("D", strtotime($date)));
	
	if (isset($exceptions)) { 															// If $exceptions is provided,
		foreach($exceptions as $ex => $ex_day) { 							// Check if today is an exception.
			$ex_day = strtotime($ex_day);
			if ($ex_day === $date) {
				return true;
			}
		}
	}

	if ($openHours[$day][0] == '00:00-00:00') { 						// Check if the store is closed all day.
		return true;
	}
	return false;
}

function isOpenNow($openHours, $getDetails = false, $exceptions) { // Check if the gate is open now.
	$time = date("G:i");
	$date = date("m/d/Y");

	return isOpen($time, $date, $openHours, $getDetails, $exceptions);
}

?>