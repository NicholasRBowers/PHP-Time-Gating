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
  'mon' => array('11:00-20:30', '03:30-4:00'),
  'tue' => array('11:00-16:00', '18:00-20:30'),
  'wed' => array('11:00-20:30'),
  'thu' => array('11:00-20:30'),
  'fri' => array('11:00-23:59', '00:00-2:00'),
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