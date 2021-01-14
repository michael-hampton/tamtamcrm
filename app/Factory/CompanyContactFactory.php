<?php

namespace App\Factory;

use App\Models\CompanyContact;

class CompanyContactFactory
{
    public static function create(int $account_id, int $user_id): CompanyContact
    {
        $vendor_contact = new CompanyContact;
        $vendor_contact->first_name = "";
        $vendor_contact->user_id = $user_id;
        $vendor_contact->account_id = $account_id;
        $vendor_contact->id = 0;

        return $vendor_contact;
    }
}
