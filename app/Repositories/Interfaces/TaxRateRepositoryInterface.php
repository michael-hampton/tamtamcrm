<?php

namespace App\Repositories\Interfaces;

use App\Models\TaxRate;
use App\Repositories\Base\BaseRepositoryInterface;

interface TaxRateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return TaxRate
     */
    public function findTaxRateById(int $id): TaxRate;

    /**
     * @param array $data
     * @param TaxRate $tax_rate
     * @return TaxRate
     */
    public function create(array $data, TaxRate $tax_rate): TaxRate;

    /**
     * @param array $data
     * @param TaxRate $tax_rate
     * @return TaxRate
     */
    public function update(array $data, TaxRate $tax_rate): TaxRate;
}
