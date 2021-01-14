<?php

namespace App\Factory;

use App\Models\CompanyToken;
use Illuminate\Support\Str;

class CompanyTokenFactory
{
    public static function create(int $account_id, int $user_id, int $domain_id): CompanyToken
    {
        $token = new CompanyToken;
        $token->user_id = $user_id;
        $token->is_web = false;
        $token->account_id = $account_id;
        $token->token = Str::random(64);
        $token->domain_id = $domain_id;

        return $token;
    }
}
