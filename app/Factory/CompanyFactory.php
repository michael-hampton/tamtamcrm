<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Company;
use App\Models\User;

class CompanyFactory
{
    /**
     * @param User $user
     * @param Account $account
     * @return Company
     */
    public function create(User $user, Account $account): Company
    {
        $company = new Company;
        $company->user_id = $user->id;
        $company->account_id = $account->id;
        $company->country_id = 225;
        $company->currency_id = 2;

        return $company;
    }
}
