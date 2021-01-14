<?php

namespace App\Traits;

use DateInterval;
use DatePeriod;
use DateTime;

trait CalculateRecurringDateRanges
{

    public function calculateDateRanges()
    {
        $begin = new DateTime($this->start_date);

        if (empty($this->expiry_date) || $this->is_endless === true) {
            $this->expiry_date = date('Y-m-d', strtotime('+1 years'));
        }

        // Declare an empty array
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = $this->calculateInterval();

        $realEnd = new DateTime($this->expiry_date);

        $period = new DatePeriod(new DateTime($this->start_date), $interval, $realEnd);

        $date_ranges = [];

        // Use loop to store date into array
        foreach ($period as $date) {
            $due_date = $this->calculateDueDate($date);

            $date_to_send = clone $date;
            $date_to_send = $this->calculateDateToSend($date_to_send);


            $date_ranges[] = [
                'expiry_date'  => $date->format('Y-m-d'),
                'due_date'     => $due_date->format('Y-m-d'),
                'date_to_send' => $date_to_send->format('Y-m-d')
            ];
        }

        return $date_ranges;
    }

    private function calculateInterval()
    {
        switch ($this->frequency) {
            case 'DAILY':
                return new DateInterval('P1D');
                break;

            case 'WEEKLY':
                return new DateInterval('P7D');
                break;

            case 'FORTNIGHT':
                return new DateInterval('P14D');
                break;

            case 'MONTHLY':
                return new DateInterval('P1M');
                break;

            case 'TWO_MONTHS':
                return new DateInterval('P2M');
                break;

            case 'THREE_MONTHS':
                return new DateInterval('P3M');
                break;

            case 'FOUR_MONTHS':
                return new DateInterval('P4M');
                break;

            case 'SIX_MONTHS':
                return new DateInterval('P6M');
                break;

            case 'YEARLY':
                return new DateInterval('P1Y');
                break;
        }

        return false;
    }

    private function calculateDueDate($date)
    {
        $due_date = clone $date;

        $days = (!empty($this->grace_period))
            ? $this->grace_period
            : ((!empty(
            $this->customer->getSetting(
                'payment_terms'
            )
            )) ? $this->customer->getSetting('payment_terms') : null);

        $due_date = $due_date->modify('+' . $days . ' day');

        return $due_date;
    }

    private function calculateDateToSend($date)
    {
        switch ($this->frequency) {
            case 'DAILY':
                return $date->modify('+1 day');
                break;

            case 'WEEKLY':
                return $date->modify('+1 week');
                break;

            case 'FORTNIGHT':
                return $date->modify('+2 week');
                break;

            case 'MONTHLY':
                return $date->modify('+1 month');
                break;

            case 'TWO_MONTHS':
                return $date->modify('+2 month');
                break;

            case 'THREE_MONTHS':
                return $date->modify('+3 month');
                break;

            case 'FOUR_MONTHS':
                return $date->modify('+4 month');
                break;

            case 'SIX_MONTHS':
                return $date->modify('+6 month');
                break;

            case 'YEARLY':
                return $date->modify('+1 year');
                break;
        }

        return false;
    }
}
