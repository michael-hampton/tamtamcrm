<?php


namespace App\Transformations;


use App\Models\CustomerGatewayToken;

trait CustomerGatewayTokenTransformable
{

    public function transformCustomerGatewayToken(CustomerGatewayToken $customer_gateway_token)
    {
        return [];
    }
}