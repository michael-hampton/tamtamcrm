<?php

namespace App\Repositories;

use App\Models\TaxRate;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\TaxRateRepositoryInterface;

class TaxRateRepository extends BaseRepository implements TaxRateRepositoryInterface
{
    /**
     *
     * @param TaxRate $taxRate
     */
    public function __construct(TaxRate $taxRate)
    {
        parent::__construct($taxRate);
        $this->model = $taxRate;
    }

    /**
     * @param int $id
     * @return TaxRate
     */
    public function findTaxRateById(int $id): TaxRate
    {
        return $this->findOneOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param TaxRate $tax_rate
     * @return TaxRate
     */
    public function update(array $data, TaxRate $tax_rate): TaxRate
    {
        $tax_rate->update($data);

        return $tax_rate;
    }

    /**
     * @param array $data
     * @param TaxRate $tax_rate
     * @return TaxRate
     */
    public function create(array $data, TaxRate $tax_rate): TaxRate
    {
        $tax_rate->fill($data)->save();

        return $tax_rate;
    }
}
