<?php

namespace App\Factory\Account;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CloneAccountToUserFactory
{
    /**
     * @param Account $account
     * @return User
     */
    public static function create(Account $account): User
    {
        $user = new User;
        $user->first_name = $account->settings->name;
        $user->last_name = '';
        $user->username = $account->support_email;
        $user->email = $account->support_email;
        $user->last_login = now();
        $user->ip = request()->ip();
        $user->confirmation_code = Str::random(config('taskmanager.key_length'));
        $user->password = Hash::make(Str::random(8));
        $user->domain_id = $account->domain_id;
        $user->is_active = 1;

        return $user;
    }
}
