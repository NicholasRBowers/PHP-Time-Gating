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

    isOpen(string $time, string $date, array $openHours [, boolean $getDetails [, array $exceptions]])

+ `$time` is the time for which you would like to know the status of the gate.
+ `$date` is the date for which you would like to know the status of the gate.
+ `$openHours` is the array in which the normal gate hours are stored.
+ `$getDetails` (default = false) tells the function if you would like to get the bounds of the current status of the gate,
    + If the gate is open, get the time it opened, and the time it will be closing;
    + If the gate is closed, get the time it closed, and the time it will be opening.
+ `$exceptions` (optional) is the array in which the exceptions (closed all day) to the normal $openHours are stored.

Under Construction
------------------
PHP Time Gating is a simple script that returns `true` or `false` based on time-of-day and-day-of-week. Simply include the script in any PHP page, adjust opening and closing hours for each day of the week and the script will output content based on the time ranges you specify.

###Easily set open hours for each day of the week
```php
$hours = array(
  'mon' => array('11:00-20:30'),
  'tue' => array('11:00-16:00', '18:00-20:30'),
  'wed' => array('11:00-20:30'),
  'thu' => array('11:00-20:30'),
  'fri' => array('11:00-20:30'),
  'sat' => array('11:00-20:30'),
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

###Customize the final output with shortcodes
Choose what you'd like to output if you're currently open, currently closed, closed for the day, or closed for an exception (such as a holiday). Smart shortcodes allow your message to include dynamic infomation such as the current day's hours or the specific reason you're closed. You can even get creative and output a custom image as shown in the example above.

```php
$open_now = "<h3>Yes, we're open! Today's hours are %open% until %closed%.</h3>";
$closed_now = "<h3>Sorry, we're closed. Today's hours are %open% until %closed%.";
$closed_all_day = "<h3>Sorry, we're closed on %day%.</h3>";
$exception = "<h3>Sorry, we're closed for %exception%.</h3>";
```

There's no need to copy/paste the code above... it's all included in the download. Please report any bugs or issues here on GitHub. I'd love to hear your ideas for improving this script or see how you've used it in your latest project.