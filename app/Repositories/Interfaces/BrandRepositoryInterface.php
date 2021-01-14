<?php

namespace App\Repositories\Interfaces;

use App\Models\Brand;
use App\Models\Product;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array $data
     * @param Brand $brand
     * @return Brand
     */
    public function save(array $data, Brand $brand): Brand;

    /**
     * @param int $id
     * @return Brand
     */
    public function findBrandById(int $id): Brand;

    /**
     * @return bool
     */
    public function deleteBrand(): bool;

    /**
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listBrands($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;

    /**
     * @param Product $product
     * @return mixed
     */
    public function saveProduct(Product $product);
}