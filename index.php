<?php

// Default timezone is set to America/Los_Angeles in the class constructor.

require 'assets/MyPaydateCalculator.php';

$calc = new MyPaydateCalculator();

$paydateModel = 'MONTHLY'; // Replace this value to any of these MONTHLY, BIWEEKLY, WEEKLY
$paydateOne = date('Y-m-d'); // If today is weekend or a holiday, please define date manually for a week day for testing. ie; '2016-07-05'
$numberOfPaydates = 10; // number of pay dates

try {
    $PayDates = $calc->calculateNextPaydates($paydateModel, $paydateOne, $numberOfPaydates);

    foreach ($PayDates as $PayDateVal) {
        echo $PayDateVal . '<br />';
    }
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}