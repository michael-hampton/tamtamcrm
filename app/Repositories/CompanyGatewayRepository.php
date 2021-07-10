<?php

namespace App\Repositories;

use App\Components\InvoiceCalculator\GatewayCalculator;
use App\Models\CompanyGateway;
use App\Repositories\Base\BaseRepository;

/**
 * Class CompanyGatewayRepository
 * @package App\Repositories
 */
class CompanyGatewayRepository extends BaseRepository
{
    /**
     * AccountRepository constructor.
     * @param CompanyGateway $company_gateway
     */
    public function __construct(CompanyGateway $company_gateway)
    {
        parent::__construct($company_gateway);
        $this->model = $company_gateway;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return CompanyGateway
     */
    public function findCompanyGatewayById(int $id): CompanyGateway
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param $gateway_key
     * @return mixed
     */
    public function getCompanyGatewayByGatewayKey(string $gateway_key): ?CompanyGateway
    {
        return $this->model->where('gateway_key', '=', $gateway_key)->first();
    }

    /**
     * @param array $data
     * @param CompanyGateway $company_gateway
     * @return CompanyGateway
     */
    public function create(array $data, CompanyGateway $company_gateway): CompanyGateway
    {
        if (!empty($data['charges'])) {
            $data['charges'] = $this->saveFees($company_gateway, $data['charges']);
        }

        $company_gateway->fill($data);
        $company_gateway->save();

        return $company_gateway;
    }

    /**
     * @param CompanyGateway $company_gateway
     * @param $fees
     * @return array
     */
    private function saveFees(CompanyGateway $company_gateway, $fees)
    {
        $gateways = [];

        foreach ($fees as $fee) {
            $gateways[] = (new GatewayCalculator($company_gateway))
                ->setFeeAmount($fee->fee_amount)
                ->setFeePercent(!empty($fee->fee_percent) ? $fee->fee_percent : 0)
                ->setTaxRate('tax_rate', isset($fee->tax) ? $fee->tax : 0)
                ->setTaxRate('tax_2', isset($fee->tax_2) ? $fee->tax_2 : 0)
                ->setTaxRate('tax_3', isset($fee->tax_3) ? $fee->tax_3 : 0)
                ->setTaxRateName('tax_rate_name', isset($fee->tax_rate_name) ? $fee->tax_rate_name : '')
                ->setTaxRateName('tax_rate_name_2', isset($fee->tax_rate_name_2) ? $fee->tax_rate_name_2 : '')
                ->setTaxRateName('tax_rate_name_3', isset($fee->tax_rate_name_3) ? $fee->tax_rate_name_3 : '')
                ->setMinLimit(!empty($fee->min_limit) ? $fee->min_limit : 0)
                ->setMaxLimit(!empty($fee->max_limit) ? $fee->max_limit : 0)
                ->toObject();
        }

        return $gateways;
    }

    /**
     * @param array $data
     * @param CompanyGateway $company_gateway
     * @return CompanyGateway
     */
    public function update(array $data, CompanyGateway $company_gateway): CompanyGateway
    {
        if (!empty($data['charges'])) {
            $data['charges'] = $this->saveFees($company_gateway, $data['charges']);
        }

        $company_gateway->update($data);

        return $company_gateway;
    }
}
