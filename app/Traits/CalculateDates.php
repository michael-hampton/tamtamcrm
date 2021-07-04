<?php

namespace App\Traits;

use App\Models\Reminders;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

trait CalculateDates
{
    use DateFormatter;

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

    public function calculateDate($frequency)
    {
        switch ($frequency) {
            case 'DAILY':
                $date = Carbon::today()->addDay();
                break;

            case 'WEEKLY':
                $date = Carbon::today()->addWeek();
                break;

            case 'FORTNIGHT':
                $date = Carbon::today()->addWeeks(2);
                break;

            case 'MONTHLY':
                $date = Carbon::today()->addMonthNoOverflow();
                break;

            case 'TWO_MONTHS':
                $date = Carbon::today()->addMonthsNoOverflow(2);
                break;

            case 'THREE_MONTHS':
                $date = Carbon::today()->addMonthsNoOverflow(3);
                break;

            case 'FOUR_MONTHS':
                $date = Carbon::today()->addMonthsNoOverflow(4);
                break;

            case 'SIX_MONTHS':
                $date = Carbon::today()->addMonthsNoOverflow(6);
                break;

            case 'YEARLY':
                $date = Carbon::today()->addYear();
                break;
            default:
                $date = Carbon::today();
                break;
        }

        return !empty($this->account->settings->time_to_send) ? $this->convertTimezone($this, $date) : $date;
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

    public function getNextReminderDateToSend(): ?string
    {
        $reminders = Reminders::query()->where('account_id', $this->account_id)->where('enabled', true)->get();

        if ($reminders->count() === 0) {
            return null;
        }

        $date_to_send = null;

        foreach ($reminders as $key => $reminder) {
            $date = $reminder->scheduled_to_send === 'after_invoice_date' ? $this->date : $this->due_date;

            $next_send_date = $reminder->scheduled_to_send === 'before_due_date' ? Carbon::parse($date)->subDays($reminder->number_of_days_after) : Carbon::parse($date)->addDays($reminder->number_of_days_after);

            // force first otherwise only set earliest date
            if ($key > 0 && !empty($this->date_to_send) && Carbon::parse($this->date_to_send)->isBefore($next_send_date)) {
                continue;
            }

            $date_to_send = $next_send_date->format('Y-m-d');
        }

        return !empty($this->account->settings->time_to_send) ? $this->convertTimezone($this, $date_to_send) : $date_to_send;
    }
}
