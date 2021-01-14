<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Group;
use App\Models\User;

class GroupFactory
{
    public static function create(Account $account, User $user): Group
    {
        $gs = new Group;
        $gs->name = '';
        $gs->account_id = $account->id;
        $gs->user_id = $user->id;
        $gs->settings = '{}';

        return $gs;
    }
}
