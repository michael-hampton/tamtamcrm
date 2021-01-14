<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Models\Product;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    /**
     * BrandRepository constructor.
     * @param Brand $brand
     */
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
        $this->model = $brand;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param Brand $brand
     * @return Brand
     */
    public function save(array $data, Brand $brand): Brand
    {
        $brand->fill($data);
        $brand->save();
        return $brand;
    }

    /**
     * @param int $id
     * @return Brand
     */
    public function findBrandById(int $id): Brand
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteBrand(): bool
    {
        return $this->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listBrands($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    /**
     * @return Collection
     */
    public function listProducts(): Collection
    {
        return $this->model->products()->get();
    }

    /**
     * @param Product $product
     */
    public function saveProduct(Product $product)
    {
        $this->model->products()->save($product);
    }

    /**
     * Dissociate the products
     */
    public function dissociateProducts()
    {
        $this->model->products()->each(
            function (Product $product) {
                $product->brand_id = null;
                $product->save();
            }
        );
    }
}
