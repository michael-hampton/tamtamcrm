<?php

namespace App\Repositories;

use App\Models\TaxRate;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\TaxRateRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

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
     * Return the courier
     *
     * @param int $id
     *
     * @return TaxRate
     * @throws CourierNotFoundException
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
     * Return all the tax rates
     *
     * @param string[] $columns
     * @param string $order
     * @param string $sort
     * @return Collection|mixed
     */
    public function listTaxRates($columns = ['*'], string $order = 'id', string $sort = 'desc'): Collection
    {
        return $this->all($columns, $order, $sort);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteTaxRate()
    {
        return $this->delete();
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchTaxRate(string $text = null): Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchTaxRate($text)->get();
    }

    public function save($data, TaxRate $taxRate): ?TaxRate
    {
        $taxRate->fill($data);
        $taxRate->save();

        return $taxRate;
    }
}
