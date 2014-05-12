<?php
require_once('time-gate-hours.php');

print_r(isOpenNow($openHours, true));

function isOpen($time, $date, $openHours, $getDetails = false, $exceptions) { // Check if the gate is open during a specific time.
  $time = strtotime($time); 																									// Prepare variables.
  $days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
  $day = strtolower(date("D", strtotime($date)));

  if (isClosedAllDay($date, $openHours, $exceptions)) { 											// Check if the gate is closed all day.
  	if ($getDetails) {

    }
    // If getDetails
      // go to the first entry of the next day
      // go to the last entry of the previous day
      // Keep going until you find SOMETHING.
    return false;
  }

  if (count($openHours[$day]) === 0) { 																				// Check if the gate is open all day.
    // if getDetails,
      // go to next day.
      // if the next day's first open entry is 00:00
        // return it's corresponding ending limit
      // else, return 00:00.
  	return true;
  } else {
  	foreach($openHours[$day] as $range) { 																		// Check if the gate is open.
  		$range = explode("-", $range);
  		$start = strtotime($range[0]);
  		$end = strtotime($range[1]);
  		if (($start <= $time) && ($time <= $end)) {                             // If the gate is open,
  			if ($getDetails) {
          if ($end === strtotime('23:59') || $end === strtotime('23:59:59')) { // The case when the gate is open past midnight.
            $tomorrow = array_search($day, $days) + 1;
            if ($tomorrow === 7) {
              $tomorrow = 0;
            }
            if (isOpen('00:00', date('m/d' , strtotime($date."+1 days")), $openHours, false, $exceptions)) {
              $range = explode("-", $openHours[$days[$tomorrow]][0]);
              // Need to make sure that $end doesn't equal 00:00 (RE: adding a day)
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
      // Where are you in the day's array?  These are open values, not closed values!
        // Beginning - go to the previous day's ending value and the next slot's starting value.
        // End - go to the previous slot's ending value and the next day's starting value.
        // Middle - go to the previous slot's ending value and the next slot's starting value.
        // Beginning and End - 
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