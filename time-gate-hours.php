<?php
// Set your timezone (codes listed at http://php.net/manual/en/timezones.php).
// Delete the following line if you've already defined a timezone elsewhere.
date_default_timezone_set('America/New_York'); 

// Define daily hours for which the time gate will be open.
// Times must be in 24-hour format, separated by dash.
// If the gate is to be open for the whole day, leave the array for that day empty.
// If the gate is to be closed for the whole day, set to 00:00-00:00.
// If there are multiple open times in one day, enter time ranges separated by a comma.
// If the gate is open late (ie. 6pm - 1am), add hours after midnight to the next day (ie. 00:00-1:00).
$openHours = array(
  'mon' => array('11:00-20:30'),                  // Normal.
  'tue' => array('11:00-16:00', '18:00-22:30'),   // Multiple open times.
  'wed' => array(''),                               // Open all day.
  'thu' => array('00:00-2:00','11:00-23:59'),                  // Open past midnight.
  'fri' => array('00:00-2:00', '11:00-20:30'),
  'sat' => array('11:00-20:00'),
  'sun' => array('11:00-20:30')
);

// Optional: add exceptions (great for holidays etc.)
// Works best with format day/month
// Leave array empty if no exceptions
$exceptions = array(
  //'Christmas' => '10/22',
  //'New Years Day' => '1/1'
);
?>