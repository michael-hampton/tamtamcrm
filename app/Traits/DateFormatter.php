<?php


namespace App\Traits;


use Carbon\Carbon;
use Exception;

trait DateFormatter
{

    public function formatDate($entity, $value)
    {
        $date_format = $this->getDateFormat($entity);
        $date_format = $this->convertDateFormat($date_format);

        try {
            return Carbon::parse($value)->format($date_format);
        } catch (Exception $e) {
            return '';
        }

        return '';
    }

    private function getDateFormat($entity)
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
        $date_format = $this->getDateFormat($entity);
        $date_format = $this->convertDateFormat($date_format);

        try {
            return Carbon::parse($value)->format($date_format . ' g:i a');
        } catch (Exception $e) {
            return '';
        }

        return '';
    }

}
