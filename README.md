PHP Time Gating
===============
*Adapted from [coryetzkorn's](https://github.com/coryetzkorn) [php store hours](https://github.com/coryetzkorn/php_store_hours).*

---------------------------------------------------------------------------

**Repurposed by**: [@NicholasRBowers](http://twitter.com/NicholasRBowers)  
**Original Author**: [coryetzkorn](https://github.com/coryetzkorn)

---------------------------------------------------------------------------

Objective
---------
The idea was to create a conceptual time-dependent gating function which is capable of returning `true` or `false` based on a set of open/close hours and a set of exceptions.  This allows developers to display alternate content in the frontend (displaying an open or closed sign) or changing settings in the backend (opening and closing an ecommerce store) all depending on the time-of-day or day-of-the-year.

Documentation
-------------
PHP Time Gating is an script that can return `true` or `false` based on time-of-day and day-of-week.  Simply include the script in any PHP page and adjust the opening and closing hours for each day of the week, and the script will return either the status of the gate, or a detailed summary of the gate's status.

First, set the open hours for each day of the week:

```php
$openHours = array(
  'mon' => array('11:00-20:30'),                  // Normal.
  'tue' => array('11:00-16:00', '18:00-22:30'),   // Multiple open times.
  'wed' => array('00:00-00:00'),                  // Open all day.
  'thu' => array('00:00-2:00','11:00-23:59'),     // Open past midnight.
  'fri' => array('00:00-2:00', '11:00-20:30'),
  'sat' => array(),                               // Closed all day.
  'sun' => array('11:00-20:30')
);
```

Then, optionally add exceptions for dates you'd like to close the gate completely (a holiday perhaps):

```php
$exceptions = array(
  'Christmas' => '10/22',
  'New Years Day' => '1/1'
);
```

The `getGateStatus()` function then compares the gate hours and exceptions with a date and time, and returns `true` if the gate is open at the specified time, or `false` otherwise.

```php
getGateStatus([string $time, [string $date]]);
```

+ `$time` is the time for which you would like to know the status of the gate (e.g. '15:42').
+ `$date` is the date for which you would like to know the status of the gate (e.g. '11/24').

If `getGateStatus()` is called without passing any parameters, it returns `true` if the gate is open now, or `false` otherwise.

The `getGateDetails()` function returns a little more information, but takes the same parameters.

```php    
getGateDetails([string $time, [string $date]]);
```

This function will return an array.  The first member of the array is the open (`true`) or closed (`false`) status of the gate.  The second and third members are the timestamps corresponding to the beginning and ending of that status.  For example, if `getGateDetails` is set to check the gate on Tuesday, during which it is set to be open all day, then the second member of the returned array will be the time on Monday that the gate opened, and third member will be the time on Wednesday that the gate will close.  If, instead, the gate status is closed when `getGateDetails` is called, the second member of the returned array will be the time the gate had closed, and the third will be the time that the gate next opens.

Examples
--------
Using the open hours and exceptions settings specified above:

```php
echo getGateStatus();
```

Echos `true` or `false` based on the current time.

```php
echo getGateStatus('10:00', '05/16/2014');
```

Echos `false`.

```php
print_r(getGateDetails('10:00', '05/16/2014'));
```

Prints `Array ( [0] => [1] => 1400220000 [2] => 1400252400 )`.

This becomes more useful when combined with `PHP`'s `date()` function to select the format in which you'd like to display the timestamp:

```php
$details = getGateDetails('10:00', '05/16/2014');
if ($details[0]) {
  echo 'Come on in!  We just opened at '.date('G:i m/d', $details[1]).', and we close at '.date('G:i m/d/Y', $details[2]).'.';
} else {
  echo 'We\'re sorry.  We closed at '.date('g:ia', $details[1]).'.  We\'ll be open again at '.date('g:ia', $details[2]).'.';
}
```

Displays `We're sorry. We close at 2:00am. We'll be open again at 11:00am.`  For more on the use of `PHP`'s `date()` function, see the [documentation](http://php.net/manual/en/function.date.php).

Comments
--------
Please report any bugs or issues here on GitHub. I'd love to hear your ideas for improving this script or see how you've used it in your latest project.