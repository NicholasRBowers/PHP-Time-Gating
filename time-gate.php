<?php
require_once('time-gate-hours.php');

echo date('G:i').'<br />';
$ret = isOpen('9:59', '05/14', true);
print_r($ret);
echo '<br />'.date('G:i m/d', $ret[1]);
echo '<br />'.date('G:i m/d', $ret[2]);

function isOpen($time = NULL, $date = NULL, $getDetails = false, $iteration = 0) {
  // Initialize variables.
  if ($time === NULL) $time = date("G:i");
  if ($date === NULL) $date = date("m/d");
  $openHours = $GLOBALS['openHours'];
  $exceptions = $GLOBALS['exceptions'];
  $day = strtolower(date("D", strtotime($date)));
  $tomorrowDate = date('m/d' , strtotime($date."+1 days"));
  $tomorrowDay = strtolower(date("D", strtotime($date."+1 days")));
  $yesterdayDate = date('m/d' , strtotime($date."-1 days"));
  $yesterdayDay = strtolower(date("D", strtotime($date."-1 days")));

  // * All day checks * //
  // Check if the gate is closed all day.
  if (isClosedAllDay($date)) {
    if ($getDetails) {
      $start = $end = '';
      if ($iteration <= 0) $start = isOpen(false, $yesterdayDate, true, $iteration - 1);
      if ($iteration >= 0) $end = isOpen(false, $tomorrowDate, true, $iteration + 1);
      if ($iteration < 0) return $start;
      elseif ($iteration > 0) return $end;
      else return array(false, $start, $end);
    } else { return false; }
  //
  // Check if the gate is open all day.
  } elseif (isOpenAllDay($date)) {
    if ($getDetails) {
      $start = $end = '';
      if ($iteration <= 0) $start = isOpen(true, $yesterdayDate, true, $iteration - 1);
      if ($iteration >= 0) $end = isOpen(true, $tomorrowDate, true, $iteration + 1);
      if ($iteration < 0) return $start;
      elseif ($iteration > 0) return $end;
      else return array(true, $start, $end);
    } else { return true; }
  }

  // * After the initial all-day checks, we break into one of four recursive modes for getDetails * //
  // 1. If the gate is closed, and we're getting details for yesterday,
  if ($time === false && $iteration < 0) return getBounds($openHours[$day][count($openHours[$day]) - 1], 'END');
  //
  // 2. If the gate is closed, and we're getting details for tomorrow,
  elseif ($time === false && $iteration > 0) return getBounds($openHours[$day][0], 'START');
  //
  // 3. If the gate is open, and we're getting details for yesterday,
  elseif ($time === true && $iteration < 0) {
    $bounds = getBounds($openHours[$day][count($openHours[$day]) - 1]);
    if ($bounds[1] === strtotime('23:59') || $bounds[1] === strtotime('23:59:59')) return $bounds[0];
    else return strtotime('00:00');
  //
  // 4. If the gate is open, and we're getting details for tomorrow,
  } elseif ($time === true && $iteration > 0) {
    $bounds = getBounds($openHours[$day][0]);
    if ($bounds[0] === strtotime('00:00') || $bounds[0] === strtotime('00:00:00')) return $bounds[1];
    else return strtotime('00:00');
  
  // We break out of the recursive modes.
  } else {
    $openTimes = array();
    $closeTimes = array();

    foreach($openHours[$day] as $range) {
      $bounds = getBounds($range);
      array_push($openTimes, $bounds[0]);
      array_push($closeTimes, $bounds[1]);
    }

    // * Check open time frames * //
    foreach($openTimes as $i => $openTime) {
      // If we've passed the time,
      if ($openTime > $time) break;
      // If the gate is open,
      if (($openTime <= $time) && ($time <= $closeTimes[$i])) {
        if ($getDetails) {
          if ($openTime === strtotime('00:00:00')) $openTime = isOpen(true, $yesterdayDate, true, $iteration - 1);
          if ($closeTimes[$i] === strtotime('23:59:59') || $closeTimes[$i] === strtotime('23:59')) $closeTimes[$i] = isOpen(true, $tomorrowDate, true, $iteration + 1);
          return array(true, $openTime, $closeTimes[$i]);
        }
        return true;
      }
    }
    //
    // * Check close time frames * //
    // Edge cases.
    if ($getDetails) {
      if ($time < $openTimes[0]) {
        return array(false, isOpen(false, $yesterdayDate, true, -1), $openTimes[0]);
      } elseif ($time > $closeTimes[count($closeTimes) - 1]) {
        return array(false, $closeTimes[count($closeTimes) - 1], isOpen(false, $tomorrowDate, true, 1));
      }
    } else {
      return false;
    }
    //
    // Mid-day cases.
    foreach($closeTimes as $j => $closeTime) {
      // If we've passed the time,
      if ($closeTime > $time) break;
      // If the gate is closed,
      if (($closeTime <= $time) && ($time <= $openTimes[$j])) {
        if ($getDetails) return array(false, $closeTime, $openTimes[$j]);
        return false;
      }
    }
  }
}

function isClosedAllDay($date = NULL) {
  // Initialize variables.
  if ($date === NULL) { $date = date("m/d"); }
  $openHours = $GLOBALS['openHours'];
  $exceptions = $GLOBALS['exceptions'];
  $day = strtolower(date("D", strtotime($date)));

  // Check if the store is closed all day.
  if (count($openHours[$day]) === 0 || $openHours[$day][0] == '') {
    return true;
  } else {
    // Check if today is an exception.
    foreach($exceptions as $ex => $exDay) {
      $exDay = strtotime($exDay);
      if ($exDay === $date) {
        return true;
      }
    }
    return false;
  }
}

function isOpenAllDay($date = NULL) {
  // Initialize variables.
  if ($date === NULL) { $date = date("m/d"); }
  $openHours = $GLOBALS['openHours'];
  $day = strtolower(date("D", strtotime($date)));

  // Check if the gate is open all day.
  if($openHours[$day][0] === '00:00-00:00') {
    return true;
  } else {
    return false;
  }
}

function getBounds($range, $mode = 0) {
  $range = explode("-", $range);
  $start = strtotime($range[0]);
  $end = strtotime($range[1]);
  if ($mode === 'START') {
    return $start;
  } elseif ($mode === 'END') {
    return $end;
  } else {
    return array($start, $end);
  }
}

?>