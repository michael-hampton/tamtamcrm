<?php


namespace App\Components\Payment\Gateways;


use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\CustomerGateway;

class GatewayFactory
{

    /**
     * @var CustomerGateway
     */
    private CustomerGateway $customer_gateway;

    /**
     * @var CompanyGateway
     */
    private CompanyGateway $company_gateway;

    /**
     * GatewayFactory constructor.
     * @param CustomerGateway $customer_gateway
     * @param CompanyGateway $company_gateway
     */
    public function __construct(CustomerGateway $customer_gateway, CompanyGateway $company_gateway)
    {
        $this->customer_gateway = $customer_gateway;
        $this->company_gateway = $company_gateway;
    }

    /**
     * @param Customer $customer
     * @return Authorize|Stripe|bool
     */
    public function create(Customer $customer)
    {
        switch ($this->company_gateway->gateway_key) {
            case '13bb8d58':
                return new Stripe($customer, $this->customer_gateway, $this->company_gateway);
            case '8ab2dce2':
                return new Authorize($customer, $this->customer_gateway, $this->company_gateway);
        }
    }
}