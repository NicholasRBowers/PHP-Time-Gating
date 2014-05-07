<?php
require_once('time-gate-hours.php');

print_r(isOpenNow($openHours, true));

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
  $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
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
          if ($end === strtotime('23:59') || $end === strtotime('23:59:59')) { // The case when the gate is open past midnight.
            $key = array_search($day, $days) + 1;
            if ($key === 7) {
              $key = 0;
            }
            if (isOpen('00:00', date('m/d' , strtotime($date."+1 days")), $openHours, false, $exceptions)) {
              $range = explode("-", $openHours[$days[$key]][0]);
              $end = strtotime($range[1]."+1 days");
            }
          }
          // Change these back to just timestamps.
  				return [true, date('l F j, Y g:i a', $start), date('l F j, Y g:i a', $end)];
  			} else {
  				return true;
  			}
  		}
  	}
  	if ($getDetails) {
      // Find the closest two time frames and use those values.
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