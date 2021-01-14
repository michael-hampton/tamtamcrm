<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\User;
use Illuminate\Support\Str;

class CustomerContactFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @param Customer $customer
     * @return CustomerContact
     */
    public static function create(Account $account, User $user, Customer $customer): CustomerContact
    {
        $customer_contact = new CustomerContact;
        $customer_contact->user_id = $user->id;
        $customer_contact->account_id = $account->id;
        $customer_contact->contact_key = Str::random(40);
        $customer_contact->customer_id = $customer->id;
        $customer_contact->id = 0;

        return $customer_contact;
    }
}
