<?php
require_once('time-gate-hours.php');

function isOpen($time, $date, $openHours, $exceptions) { // Check if the gate is open during a specific time.
  $time = strtotime($time); 																									// Prepare variables.
  $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
  $day = strtolower(date("D", strtotime($date)));

  if (isClosedAllDay($date, $openHours, $exceptions)) { 											// Check if the gate is closed all day.
    return false;
  }

  if ($openHours[$day][0] == '00:00-00:00') { 															  // Check if the gate is open all day.
  	return true;
  } else {
  	foreach($openHours[$day] as $range) { 																		// Check if the gate is open.
  		$range = explode("-", $range);
  		$start = strtotime($range[0]);
  		$end = strtotime($range[1]);
  		if (($start <= $time) && ($time <= $end)) {                             // If the gate is open,
  			return true;
  			}
  		}
  	}
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

	if (count($openHours[$day]) === 0) { 						        // Check if the store is closed all day.
		return true;
	}
	return false;
}

function isOpenNow($openHours, $exceptions) { // Check if the gate is open now.
	$time = date("G:i");
	$date = date("m/d/Y");

	return isOpen($time, $date, $openHours, $exceptions);
}

?>