## How To Take This Test

Use the PaydateCalculatorInterface and create a class in PHP5 OOP called MyPaydateCalculator

## Rules
This is a timed test

* Given a paydate and a paydate model, MyPaydateCalculator must be able to return the next 10 paydates after today
* MyPaydateCalculator must run without generating errors or warnings
* A valid paydate cannot fall on today, a weekend or a holiday
* If a paydate falls on a weekend, increment the date by one day until a valid paydate is reached.
* If a paydate falls on a holiday, decrement the date by one day until a valid paydate is reached.
* Holiday adjustments takes precedence over weekend adjustments
* The initial paydate given to your class should not be adjusted, even if it falls on a weekend or a holiday

## Holidays
$holidays = ['01-01-2014','20-01-2014','17-02-2014','26-05-2014','04-07-2014','01-09-2014','13-10-2014','11-11-2014','27-11-2014','25-12-2014','01-01-2015','19-01-2015','16-02-2015','25-05-2015','03-07-2015','07-09-2015','12-10-2015','11-11-2015','26-11-2015','25-12-2015'];

(feel free to adjust to current year)

## Paydate Models:
* MONTHLY - A person is paid on the same day of the month every month, for instance, 1/17/2012 and 2/17/2012
* BIWEEKLY - A person is paid on the same day of the week every other week, for instance, 4/6/2012 and 4/20/2012
* WEEKLY - A person is paid on the same day of the week every week, for instance 4/9/2012 and 4/16/2012
