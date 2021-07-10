<?php

namespace App\Factory\Lead;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;

class CloneLeadToCustomerFactory
{
    /**
     * @param Lead $lead
     * @param User $user
     * @param Account $account
     * @return Customer
     */
    public static function create(Lead $lead, User $user, Account $account): Customer
    {
        $client_contact = new Customer();
        $client_contact->account_id = $account->id;
        $client_contact->user_id = $user->id;
        $client_contact->name = $lead->first_name . ' ' . $lead->last_name;
        $client_contact->phone = $lead->phone;
        $client_contact->website = $lead->website;
        $client_contact->customer_note = $lead->customer_note;
        $client_contact->internal_note = $lead->internal_note;
        $client_contact->currency_id = 2;

        return $client_contact;
    }
}
