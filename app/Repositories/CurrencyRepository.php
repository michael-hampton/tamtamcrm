<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CurrencyRepositoryInterface;
use Illuminate\Support\Collection;

class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{
    /**
     * CountryRepository constructor.
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        parent::__construct($currency);
        $this->model = $currency;
    }

    /**
     * List all the currencies
     *
     * @param string $order
     * @param string $sort
     * @return Collection
     */
    public function listCurrencies(string $order = 'id', string $sort = 'desc'): Collection
    {
        return $this->model->get();
    }

    public function findCurrencyById(int $id): Currency
    {
        return $this->findOneOrFail($id);
    }
}
