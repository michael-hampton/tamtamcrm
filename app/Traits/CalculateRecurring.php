<?php


namespace App\Traits;


use Carbon\Carbon;

trait CalculateRecurring
{

    public function calculateDate($frequency)
    {
        switch ($frequency) {
            case 'DAILY':
                return Carbon::today()->addDay();
                break;

            case 'WEEKLY':
                return Carbon::today()->addWeek();
                break;

            case 'FORTNIGHT':
                return Carbon::today()->addWeeks(2);
                break;

            case 'MONTHLY':
                return Carbon::today()->addMonthNoOverflow();
                break;

            case 'TWO_MONTHS':
                return Carbon::today()->addMonthsNoOverflow(2);
                break;

            case 'THREE_MONTHS':
                return Carbon::today()->addMonthsNoOverflow(3);
                break;

            case 'FOUR_MONTHS':
                return Carbon::today()->addMonthsNoOverflow(4);
                break;

            case 'SIX_MONTHS':
                return Carbon::today()->addMonthsNoOverflow(6);
                break;

            case 'YEARLY':
                return Carbon::today()->addYear();
                break;
            default:
                return Carbon::today();
                break;
        }
    }

}
