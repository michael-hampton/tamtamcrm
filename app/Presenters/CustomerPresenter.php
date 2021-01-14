<?php

namespace App\Presenters;

use App\Models\Country;
use Laracasts\Presenter\Presenter;

/**
 * Class CustomerPresenter
 * @package App\Presenters
 */
class CustomerPresenter extends Presenter
{
    /**
     * @return string
     */
    public function email()
    {
        return $this->entity->primary_contact->first() !==
        null ? $this->entity->primary_contact->first()->email : 'No Email Set';
    }

    public function shipping_address()
    {
        return $this->address(2);
    }

    public function address($type = 1)
    {
        $fields = ['address_1', 'address_2', 'city', 'country_id'];

        $address = $this->entity->addresses->where('address_type', $type)->first();

        if (empty($address) || $address->count() === 0) {
            return '';
        }

        $str = '';

        foreach ($fields as $field) {
            if (empty($address->{$field})) {
                continue;
            }

            if ($field === 'country_id') {
                $country = Country::where('id', $address->{$field})->first();
                $str .= $country->name;
                continue;
            }

            $str .= $address->{$field} . '<br/>';
        }

        return $str;
    }

    public function phone()
    {
        return $this->entity->phone ?: '';
    }

    public function website()
    {
        return $this->entity->website ?: '';
    }

    public function clientName()
    {
        return $this->name();
    }

    /**
     * @return string
     */
    public function name()
    {
        $contact = $this->entity->primary_contact->first();

        $contact_name = '';

        if ($contact) {
            $contact_name = $contact->first_name . ' ' . $contact->last_name;
        }

        return $this->entity->name ?: $contact_name;
    }

    public function cityStateZip($city, $state, $postalCode, $swap)
    {
        $str = $city;

        if ($state) {
            if ($str) {
                $str .= ', ';
            }
            $str .= $state;
        }

        if ($swap) {
            return $postalCode . ' ' . $str;
        } else {
            return $str . ' ' . $postalCode;
        }
    }
}
