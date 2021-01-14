<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;

class ProjectFactory
{
    public static function create(User $user, Customer $customer, Account $account): Project
    {
        $project = new Project;

        $project->customer_id = $customer->id;
        $project->account_id = $account->id;
        $project->user_id = $user->id;

        return $project;
    }
}
