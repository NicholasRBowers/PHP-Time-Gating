PHP Time Gating
===============
*Adapted from [coryetzkorn's](https://github.com/coryetzkorn) [php store hours](https://github.com/coryetzkorn/php_store_hours).*

---------------------------------------------------------------------------

**Repurposed by**: [@NicholasRBowers](http://twitter.com/NicholasRBowers)  
**Original Author**: [coryetzkorn](https://github.com/coryetzkorn)

---------------------------------------------------------------------------

Objective
---------
To create a robust set of functions which return `true` or `false` based on the current (or future) time and a set of "open" times.  This allows web developers to display alternate content in the frontend (displaying an open or closed sign) or complete certain backend functions (setting ecommerce store opened or closed).

Documentation
-------------

Compares the gate hours and exceptions with a date and time, and returns `true` if the gate is open at the specified time, or `false` otherwise.
    isOpen(string $time, string $date, array $openHours [, array $exceptions])

+ `$time` is the time for which you would like to know the status of the gate.
+ `$date` is the date for which you would like to know the status of the gate.
+ `$openHours` is the array in which the normal gate hours are stored.
+ `$exceptions` (optional) is the array in which the exceptions (closed all day) to the normal `$openHours` are stored.

Returns `true` if the gate is open right now, or `false` otherwise.
    isOpenNow(array $openHours [, array $exceptions])

Returns `true` if the gate is closed all day, or `false` otherwise.
    isClosedAllDay($date, $openHours, $exceptions)

Under Construction
------------------
PHP Time Gating is a simple script that returns `true` or `false` based on time-of-day and day-of-week. Simply include the script in any PHP page, adjust opening and closing hours for each day of the week and the script will output content based on the time ranges you specify.

###Easily set open hours for each day of the week
```php
$openHours = array(
  'mon' => array('11:00-20:30'),                  // Normal.
  'tue' => array('11:00-16:00', '18:00-22:30'),   // Multiple open times.
  'wed' => array('00:00-00:00'),                  // Open all day.
  'thu' => array('00:00-2:00','11:00-23:59'),     // Open past midnight.
  'fri' => array('00:00-2:00', '11:00-20:30'),
  'sat' => array(''),                             // Closed all day.
  'sun' => array('11:00-20:30')
);
```

###Add exceptions for specific dates / holidays
```php
$exceptions = array(
  'Christmas' => '10/22',
  'New Years Day' => '1/1'
);
```

###Make dynamic content
```html
<?php include('time-gate.php'); ?>
<h1>Gadgets Inc.</h1>
<h2>Store Status</h2>
<h3><?php if(isOpenNow()) {echo 'Open.';} else {echo 'Closed.';} ?></h3>
```

There's no need to copy/paste the code above... it's all included in the download. Please report any bugs or issues here on GitHub. I'd love to hear your ideas for improving this script or see how you've used it in your latest project.