<?php

namespace App\Transformations;

use App\Models\CompanyGateway;
use App\Models\PaymentGateway;

trait CompanyGatewayTransformable
{
    /**
     * @param CompanyGateway $company_gateway
     * @return array
     */
    protected function transformCompanyGateway(CompanyGateway $company_gateway)
    {
        $gateway = $this->transformGateway($company_gateway->gateway);

        return [
            'id'                    => (int)$company_gateway->id,
            'name'                  => !empty($company_gateway->name) ? $company_gateway->name : $gateway['name'],
            'description'           => $company_gateway->description ?: '',
            'gateway_key'           => (string)$company_gateway->gateway_key ?: '',
            'gateway'               => $gateway,
            'accepted_credit_cards' => $company_gateway->accepted_credit_cards,
            'require_cvv'           => (bool)$company_gateway->require_cvv,
            'fields'                => $company_gateway->fields,
            'config'                => $company_gateway->config,
            'mode'                  => $company_gateway->getMode(),
            'fees_and_limits'       => $company_gateway->fees_and_limits ?: '',
            'updated_at'            => $company_gateway->updated_at,
            'deleted_at'            => $company_gateway->deleted_at,
        ];
    }

    /**
     * @param PaymentGateway $gateway
     * @return array
     */
    public function transformGateway(PaymentGateway $gateway)
    {
        if (empty($gateway)) {
            return [];
        }

        return (new GatewayTransformable)->transformGateway($gateway);
    }
}
