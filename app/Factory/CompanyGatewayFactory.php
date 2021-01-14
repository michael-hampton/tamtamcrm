<?php

namespace App\Factory;

use App\Models\CompanyGateway;

class CompanyGatewayFactory
{
    public static function create(int $account_id, int $user_id): CompanyGateway
    {
        $company_gateway = new CompanyGateway;
        $company_gateway->account_id = $account_id;
        $company_gateway->user_id = $user_id;
        return $company_gateway;
    }
}
