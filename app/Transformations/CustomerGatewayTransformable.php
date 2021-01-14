<?php

namespace App\Transformations;

use App\Models\CustomerGateway;

class CustomerGatewayTransformable
{
    /**
     * @param CustomerGateway $gateway
     * @return array
     */
    public function transformGateway(CustomerGateway $gateway)
    {
        return [
            'id'                 => (int)$gateway->id,
            'customer_id'        => (int)$gateway->customer_id,
            'company_gateway_id' => (int)$gateway->company_gateway_id,
            'token'              => $gateway->token,
            'customer_reference' => $gateway->gateway_customer_reference,
            'gateway_type_id'    => $gateway->gateway_type_id,
            'meta'               => !empty($gateway->meta) ? json_decode($gateway->meta) : []
        ];
    }
}
