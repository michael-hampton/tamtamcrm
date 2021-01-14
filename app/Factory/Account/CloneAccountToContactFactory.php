<?php

namespace App\Factory\Account;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\User;
use Illuminate\Support\Str;

class CloneAccountToContactFactory
{
    /**
     * @param Account $account
     * @param Customer $customer
     * @param User $user
     * @return CustomerContact
     */
    public static function create(Account $account, Customer $customer, User $user): CustomerContact
    {
        $client_contact = new CustomerContact();
        $client_contact->account_id = $account->id;
        $client_contact->user_id = $user->id;
        $client_contact->customer_id = $customer->id;
        $client_contact->first_name = $account->settings->name;
        $client_contact->last_name = '';
        $client_contact->email = $account->settings->email;
        $client_contact->phone = $account->settings->phone;
        $client_contact->is_primary = true;
        $client_contact->contact_key = Str::random(40);


        return $client_contact;
    }
}
