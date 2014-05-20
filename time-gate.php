<?php
require_once('time-gate-hours.php');

function getGateStatus($time = NULL, $date = NULL) {
  return isOpen($time, $date);
}

function getGateDetails($time = NULL, $date = NULL) {
  return isOpen($time, $date, true);
}

function isOpen($time = NULL, $date = NULL, $getDetails = false, $iteration = 0) {
  // Initialize variables.
  if ($time === NULL) $time = date("G:i");
  if ($date === NULL) $date = date("m/d/Y");
  if ($time !== false && $time !== true) $time = strtotime($time.' '.$date);
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
    } else return false;
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
    } else return true;
  }

  // * After the initial all-day checks, we break into one of four recursive modes for getDetails * //
  // 1. If the gate is closed, and we're getting details for yesterday,
  if ($time === false && $iteration < 0) return getBounds($openHours[$day][count($openHours[$day]) - 1], $date, 'END');
  //
  // 2. If the gate is closed, and we're getting details for tomorrow,
  elseif ($time === false && $iteration > 0) return getBounds($openHours[$day][0], $date, 'START');
  //
  // 3. If the gate is open, and we're getting details for yesterday,
  elseif ($time === true && $iteration < 0) {
    $bounds = getBounds($openHours[$day][count($openHours[$day]) - 1], $date);
    if ($bounds[1] === strtotime('23:59 '.$date) || $bounds[1] === strtotime('23:59:59 '.$date)) return $bounds[0];
    else return strtotime('00:00 '.$tomorrowDate);
  //
  // 4. If the gate is open, and we're getting details for tomorrow,
  } elseif ($time === true && $iteration > 0) {
    $bounds = getBounds($openHours[$day][0], $date);
    if ($bounds[0] === strtotime('00:00 '.$date) || $bounds[0] === strtotime('00:00:00 '.$date)) return $bounds[1];
    else return strtotime('00:00 '.$date);
  
  // We break out of the recursive modes.
  } else {
    $openTimes = array();
    $closeTimes = array();

    foreach($openHours[$day] as $range) {
      $bounds = getBounds($range, $date);
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
          if ($openTime === strtotime('00:00 '.$date)) $openTime = isOpen(true, $yesterdayDate, true, $iteration - 1);
          if ($closeTimes[$i] === strtotime('23:59:59 '.$date) || $closeTimes[$i] === strtotime('23:59 '.$date)) $closeTimes[$i] = isOpen(true, $tomorrowDate, true, $iteration + 1);
          return array(true, $openTime, $closeTimes[$i]);
        }
        return true;
      }
    }
    //
    // * Check close time frames * //
    // Edge cases.
    if ($getDetails) {
      if ($time < $openTimes[0]) return array(false, isOpen(false, $yesterdayDate, true, -1), $openTimes[0]);
      elseif ($time > $closeTimes[count($closeTimes) - 1]) return array(false, $closeTimes[count($closeTimes) - 1], isOpen(false, $tomorrowDate, true, 1));
    } else return false;
    //
    // Mid-day cases.
    foreach($closeTimes as $j => $closeTime) {
      // If we've passed the time,
      if ($closeTime > $time) break;
      // If the gate is closed,
      if (($closeTime <= $time) && ($time <= $openTimes[$j + 1])) {
        if ($getDetails) return array(false, $closeTime, $openTimes[$j + 1]);
        return false;
      }
    }
  }
}

function isClosedAllDay($date = NULL) {
  // Initialize variables.
  if ($date === NULL) $date = date("m/d/Y");
  $date = strtotime($date);
  $openHours = $GLOBALS['openHours'];
  $exceptions = $GLOBALS['exceptions'];
  $day = strtolower(date("D", $date));

  // Check if the store is closed all day.
  if (count($openHours[$day]) === 0 || $openHours[$day][0] === '') return true;
  else {
    // Check if today is an exception.
    foreach($exceptions as $ex => $exDay) {
      $exDay = strtotime($exDay);
      if ($exDay === $date) return true;
    }
    return false;
  }
}

function isOpenAllDay($date = NULL) {
  // Initialize variables.
  if ($date === NULL) $date = date("m/d/Y");
  $openHours = $GLOBALS['openHours'];
  $day = strtolower(date("D", strtotime($date)));

  // Check if the gate is open all day.
  if($openHours[$day][0] === '00:00-00:00') return true;
  else return false;
}

function getBounds($range, $date = NULL, $mode = 0) {
  if ($date === NULL) $date = date("m/d/Y");
  $range = explode("-", $range);
  $start = strtotime($range[0].' '.$date);
  $end = strtotime($range[1].' '.$date);
  if ($mode === 'START') return $start;
  elseif ($mode === 'END') return $end;
  else return array($start, $end);
}

?>