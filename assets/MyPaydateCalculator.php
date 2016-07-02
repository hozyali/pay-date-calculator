<?php

require "assets/PaydateCalculatorInterface.php";

class MyPaydateCalculator implements PaydateCalculatorInterface {

    private $unit = null;
    private $count = null;

    // Set default timezone rule as constructor

    public function __construct() {
        date_default_timezone_set('America/Los_Angeles');
    }

    /**
     * This function takes a paydate model and two paydates and generates the next $numberOfPaydates paydates.
     */
    public function calculateNextPaydates($paydate_model, $paydate_one, $numberOfPaydates) {
        if (!$this->ValidateDateFormat($paydate_one)) {
            throw new Exception("Date isn't valid.");
        }
        // Check number of paydates
        $numberOfPaydates = (int) $numberOfPaydates;
        if ($numberOfPaydates <= 0) {
            throw new Exception("Number of dates must be greater than 0");
        }
        // Check if the first pay date is a weekday (no weekend or holidays)

        if (!$this->isValidPaydate($paydate_one)) {
            throw new Exception("First pay date is either a holiday or weekend. Exiting;");
        }

        $this->SetModelProperties($paydate_model);
        $adj_pay = array();
        $i = 0;
        $current_paydate = $paydate_one;
        while ($i < $numberOfPaydates) {
            // Adjust specific pay date.
            $adjusted_paydate = $current_paydate = $this->increaseDate($current_paydate, $this->count, $this->unit);
            if (!$this->isValidPaydate($adjusted_paydate)) {
                $adjusted_paydate = $this->Paydate_Adjustment($adjusted_paydate);
            }
            $adj_pay[] = $adjusted_paydate;
            $i++;
        }
        return $adj_pay;
    }

    /**
     * This function determines whether a given date in Y-m-d format is a holiday.
     */
    public function isHoliday($date) {
        if (!$this->ValidateDateFormat($date)) {
            throw new Exception("Date isn't valid.");
        }

        $holidays = [
            '2014-01-01',
            '1970-01-01',
            '2014-02-17',
            '2014-05-26',
            '2014-07-04',
            '2014-09-01',
            '2014-10-13',
            '2014-11-11',
            '2014-11-27',
            '2014-12-25',
            '2015-01-01',
            '2015-01-19',
            '2015-02-16',
            '2015-05-25',
            '2015-07-03',
            '2015-09-07',
            '2015-10-12',
            '2015-11-11',
            '2015-11-26',
            '2015-12-25'
        ];
        return in_array($date, $holidays);
    }

    /**
     * This function determines whether a given date in Y-m-d format is on a weekend.
     */
    public function isWeekend($date) {
        if (!$this->ValidateDateFormat($date)) {
            throw new Exception("Date isn't valid.");
        }

        $week_days = date('N', strtotime($date));
        // N 6 and 7 are weekend days
        return ($week_days == 6 || $week_days == 7);
    }

    /**
     * This function determines whether a given date in Y-m-d format is a valid paydate according to specification rules.
     */
    public function isValidPaydate($date) {
        // Check for valid date
        if (!$this->ValidateDateFormat($date)) {
            throw new Exception("Date isn't valid.");
        }
        // Check for holiday
        if ($this->isHoliday($date)) {
            return false;
        }
        // Check for weekend
        if ($this->isWeekend($date)) {
            return false;
        }
        // Return valid Pay Date
        return true;
    }

    /**
     * This function increases a given date in Y-m-d format by $count $units
     */
    public function increaseDate($date, $count, $unit = 'days') {
        if (!$this->ValidateDateFormat($date)) {
            throw new Exception("Date isn't valid in increaseDate");
        }

        $count = (int) $count;
        if ($count <= 0) {
            throw new Exception("Date isn't valid in increaseDate");
        }
        // using PHP DateInterval & DateTime class, check for short months
        switch ($unit) {
            case 'months':
                $d = new DateTime($date);
                $day = $d->format('j');
                $d->add(new DateInterval('P' . $count . 'M'));
                if ($d->format('j') != $day) {
                    $d->modify('last day of last month');
                }
                return $d->format('Y-m-d');
                break;
            case 'days':
                $d = new DateTime($date);
                $d->add(new DateInterval('P' . $count . 'D'));
                return $d->format('Y-m-d');
                break;

            default:
                throw new Exception("Date isn't valid in increaseDate");
                break;
        }
    }

    /**
     * This function decreases a given date in Y-m-d format by $count $units
     */
    public function decreaseDate($date, $count, $unit = 'days') {
        if (!$this->ValidateDateFormat($date)) {
            throw new Exception("Date isn't valid");
        }

        $count = (int) $count;
        if ($count <= 0) {
            throw new Exception("Count is not valid");
        }
        // using PHP DateInterval & DateTime class
        switch ($unit) {
            case 'months':
                $interval = new DateInterval('P' . $count . 'M');
                break;
            case 'days':
                $interval = new DateInterval('P' . $count . 'D');
                break;

            default:
                throw new Exception("Unit is not valid in increaseDate method");
                break;
        }
        $d = new DateTime($date);
        $d->sub($interval);
        return $d->format('Y-m-d');
    }

    /**
     * Method to adjust pay dates based on weekend and holidays
     */
    public function Paydate_Adjustment($date) {
        if (!$this->ValidateDateFormat($date)) {
            throw new Exception("Date isn't valid Paydate_Adjustment method");
        }
        while (!$this->isValidPaydate($date)) {
            // Decrease date to previous day if pay date is on holiday.
            // Holiday adjustment takes precedence over weekend adjustment
            if ($this->isHoliday($date)) {
                $date = $this->decreaseDate($date, 1, 'days');
                // check to see if holiday fell on a Sunday or Monday and adjust for weekend
                while ($this->isWeekend($date)) {
                    $date = $this->decreaseDate($date, 1, 'days');
                }
            }
            // If a paydate is on a weekend, increase date to next day.
            elseif ($this->isWeekend($date)) {
                $date = $this->increaseDate($date, 1, 'days');
            } else {
                // Infinite loop prevention
                die('FATAL ERROR: Paydate_Adjustment has infinite loop');
            }
        }
        return $date;
    }

    /**
     * Method to validate the date format, make sure it is Y-m-d format
     * */
    private function ValidateDateFormat($date) {
        if ((bool) preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date) == true) {
            return (bool) strtotime($date);
        }
        return false;
    }

    /**
     * Method to set class properties
     */
    private function SetModelProperties($paydate_model) {
        switch ($paydate_model) {
            case 'WEEKLY':
                $this->unit = 'days';
                $this->count = 7;
                break;

            case 'BIWEEKLY':
                $this->unit = 'days';
                $this->count = 14;
                break;
            case 'MONTHLY':
                $this->unit = 'months';
                $this->count = 1;
                break;
            default:
                throw new Exception("Invalid paydate_model supplied");
                break;
        }
    }

}
