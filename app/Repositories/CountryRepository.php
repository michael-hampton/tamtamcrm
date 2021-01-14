<?php

namespace App\Repositories;

use App\Models\Country;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CountryRepositoryInterface;
use Illuminate\Support\Collection;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    /**
     * CountryRepository constructor.
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        parent::__construct($country);
        $this->model = $country;
    }

    /**
     * List all the countries
     *
     * @param string $order
     * @param string $sort
     * @return Collection
     */
    public function listCountries(string $order = 'id', string $sort = 'desc'): Collection
    {
        return $this->model->where('status', 1)->get();
    }
}
