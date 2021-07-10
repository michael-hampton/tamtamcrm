<?php

namespace App\Transformations;

use App\Models\CompanyGateway;
use App\Models\ErrorLog;
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
            'settings'              => $company_gateway->settings,
            'mode'                  => $company_gateway->getMode(),
            'charges'               => $company_gateway->charges ?: '',
            //'error_logs'            => $this->transformErrorLogs($company_gateway->error_logs()),
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

    /**
     * @param $error_logs
     * @return array
     */
    private function transformErrorLogs($error_logs)
    {
        if (empty($error_logs)) {
            return [];
        }

        return $error_logs->map(
            function (ErrorLog $error_log) {
                return (new ErrorLogTransformable())->transformErrorLog($error_log);
            }
        )->all();
    }
}
