<?php

namespace App\Factory;

use App\Models\User;
use Illuminate\Support\Str;

class UserFactory
{
    public static function create(int $domain_id): User
    {
        $user = new User;
        $user->last_login = now();
        $user->ip = request()->ip();
        $user->confirmation_code = Str::random(config('taskmanager.key_length'));
        $user->domain_id = $domain_id;
        $user->is_active = 1;

        return $user;
    }
}
