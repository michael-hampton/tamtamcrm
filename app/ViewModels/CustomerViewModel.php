<?php


namespace App\ViewModels;


use App\Models\Country;
use App\Models\Customer;

class CustomerViewModel extends ViewModel
{

    /**
     * @var Customer
     */
    private Customer $customer;

    /**
     * CustomerViewModel constructor.
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->customer->primary_contact->first() !==
        null ? $this->customer->primary_contact->first()->email : 'No Email Set';
    }

    public function shipping_address()
    {
        return $this->address(2);
    }

    public function address($type = 1)
    {
        $fields = ['address_1', 'address_2', 'city', 'country_id'];

        $address = $this->customer->addresses->where('address_type', $type)->first();

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
        return $this->customer->phone ?: '';
    }

    public function website()
    {
        return $this->customer->website ?: '';
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
        $contact = $this->customer->primary_contact->first();

        $contact_name = '';

        if ($contact) {
            $contact_name = $contact->first_name . ' ' . $contact->last_name;
        }

        return $this->customer->name ?: $contact_name;
    }
}