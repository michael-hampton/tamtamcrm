<?php

namespace App\Factory;

use App\Models\Customer;
use App\Models\CustomerContact;

class CloneCustomerToContactFactory
{
    public static function create(Customer $customer, $user_id, $account_id): ?CustomerContact
    {
        $client_contact = new CustomerContact();
        $client_contact->account_id = $account_id;
        $client_contact->customer_id = $customer->id;
        $client_contact->user_id = $user_id;
        $client_contact->first_name = $customer->first_name;
        $client_contact->last_name = $customer->last_name;
        $client_contact->email = $customer->email;
        $client_contact->phone = $customer->phone;

        return $client_contact;
    }
}
