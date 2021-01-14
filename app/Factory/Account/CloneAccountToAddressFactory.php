<?php

namespace App\Factory\Account;

use App\Models\Account;
use App\Models\Address;
use App\Models\Customer;

class CloneAccountToAddressFactory
{
    /**
     * @param Account $account
     * @param Customer $customer
     * @return Address
     */
    public static function create(Account $account, Customer $customer): Address
    {
        $address = new Address();
        $address->address_1 = !empty($account->settings->address1) ? $account->settings->address1 : '';
        $address->address_2 = isset($account->settings->address2) ? $account->settings->address2 : '';
        $address->customer_id = $customer->id;
        $address->zip = $account->zip;
        $address->city = !empty($account->settings->city) ? $account->settings->city : '';
        $address->country_id = !empty($account->settings->country_id) ? $account->settings->country_id : 225;
        $address->address_type = 1;
        $address->status = 1;


        return $address;
    }
}
