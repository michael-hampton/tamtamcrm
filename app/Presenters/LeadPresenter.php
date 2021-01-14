<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class CustomerPresenter
 * @package App\Presenters
 */
class LeadPresenter extends Presenter
{
    /**
     * @return string
     */
    /**
     * @return string
     */
    public function name()
    {
        return $this->entity->first_name . ' ' . $this->entity->last_name;
    }

    public function address()
    {
        $fields = ['address_1', 'address_2', 'city', 'country'];
        $str = '';

        foreach ($fields as $field) {
            if (empty($this->entity->{$field})) {
                continue;
            }

            if ($field === 'country') {
                $country = $this->entity->country;
                $str .= $country->name . '<br/>';
                continue;
            }

            $str .= $this->entity->{$field} . '<br/>';
        }

        return $str;
    }

    public function cityStateZip($city, $postalCode, $swap)
    {
        $str = $city;

        if ($swap) {
            return $postalCode . ' ' . $str;
        } else {
            return $str . ' ' . $postalCode;
        }
    }

    public function email()
    {
        return $this->entity->email;
    }

    public function phone()
    {
        return $this->entity->phone ?: '';
    }

    public function website()
    {
        return $this->entity->website ?: '';
    }
}
