<?php

namespace App\Factory\Lead;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Lead;

class CloneLeadToAddressFactory
{
    public static function create(Lead $lead, Customer $customer): Address
    {
        $client_contact = new Address();
        $client_contact->address_1 = $lead->address_1;
        $client_contact->address_2 = $lead->address_2;
        $client_contact->customer_id = $customer->id;
        $client_contact->zip = $lead->zip;
        $client_contact->city = $lead->city;
        $client_contact->country_id = 225;
        $client_contact->address_type = 1;
        $client_contact->status = 1;


        return $client_contact;
    }
}
