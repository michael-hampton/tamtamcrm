<?php


namespace App\Traits;


use Carbon\Carbon;
use Exception;

trait DateFormatter
{

    public function formatDate($entity, $value)
    {
        $date_format = $this->getDateFormatForUser($entity);
        $date_format = $this->convertDateFormat($date_format);

        try {
            return Carbon::parse($value)->format($date_format);
        } catch (Exception $e) {
            return '';
        }

        return '';
    }

    private function getTimezone($entity)
    {
        return (get_class($entity) === 'App\Models\Customer')
            ? $entity->getSetting(
                'timezone'
            )
            : ((!empty($entity->customer)) ? $entity->customer->getSetting(
                'timezone'
            ) : $entity->account->settings->timezone);
    }

    private function getDateFormatForUser($entity)
    {
        return (get_class($entity) === 'App\Models\Customer')
            ? $entity->getSetting(
                'date_format'
            )
            : ((!empty($entity->customer)) ? $entity->customer->getSetting(
                'date_format'
            ) : $entity->account->settings->date_format);
    }

    private function convertDateFormat($date_format)
    {
        switch ($date_format) {
            case 'DD/MMM/YYYY':
                return 'D M Y';
            case 'DD-MMM-YYYY':
                return 'D-M-Y';
            case 'DD-MMMM-YYYY':
                return 'D-M-Y';
        }

        return $date_format;
    }

    public function formatDatetime($entity, $value)
    {
        $date_format = $this->getDateFormatForUser($entity);
        $date_format = $this->convertDateFormat($date_format);

        try {
            return Carbon::parse($value)->format($date_format . ' g:i a');
        } catch (Exception $e) {
            return '';
        }

        return '';
    }

    public function convertTimezone($entity, $date)
    {
        //https://blog.serverdensity.com/handling-timezone-conversion-with-php-datetime/
        $hours = $entity->account->settings->time_to_send;

        $userTimezone = new \DateTimeZone($entity->account->settings->timezone);
        $gmtTimezone = new \DateTimeZone('GMT');
        $myDateTime = new \DateTime($date, $gmtTimezone);
        $offset = $userTimezone->getOffset($myDateTime) + ($hours * 3600);
        $myInterval = \DateInterval::createFromDateString((string)$offset . 'seconds');
        $myDateTime->add($myInterval);

        return $myDateTime->format('Y-m-d H:i:s');
    }
}
