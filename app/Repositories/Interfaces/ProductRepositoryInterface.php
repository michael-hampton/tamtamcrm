<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Repositories\Base\BaseRepositoryInterface;
use App\Requests\SearchRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Support;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return mixed
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param $data
     * @param Product $product
     * @return Product|null
     */
    public function save(array $data, Product $product): ?Product;

    /**
     * @param int $id
     * @return Product
     */
    public function findProductById(int $id): Product;

    /**
     *
     */
    public function deleteProduct(): bool;

    /**
     * @param string $slug
     * @return Product
     */
    public function findProductBySlug(string $slug): Product;

    /**
     *
     */
    public function getCategories(): Collection;

    /**
     *
     */
    public function findBrand();

    /**
     *
     * @param Category $objCategory
     * @return Support
     */
    public function filterProductsByCategory(Category $objCategory): Support;

    /**
     * @param ProductAttribute $productAttribute
     * @param Product $product
     * @return ProductAttribute
     */
    public function saveProductAttributes(ProductAttribute $productAttribute, Product $product): ProductAttribute;

    /**
     *
     */
    public function listProductAttributes(): Collection;

    /**
     *
     * @param ProductAttribute $productAttribute
     * @param Product $product
     * @return bool|null
     */
    public function removeProductAttribute(ProductAttribute $productAttribute, Product $product): ?bool;

    /**
     *
     * @param Category $category
     * @param Request $request
     */
    public function getProductsByDealValueAndCategory(Category $category, Request $request);
}
