<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Cases;
use App\Models\Customer;
use App\Models\CustomerGatewayToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CustomerGatewayTokenFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @param Customer $customer
     * @return CustomerGatewayToken
     */
    public static function create(Account $account, User $user, Customer $customer): CustomerGatewayToken
    {
        $customer_gateway_token = new CustomerGatewayToken();
        $customer_gateway_token->setAccount($account);
        $customer_gateway_token->setUser($user);
        $customer_gateway_token->setCustomer($customer);
        return $customer_gateway_token;
    }
}
