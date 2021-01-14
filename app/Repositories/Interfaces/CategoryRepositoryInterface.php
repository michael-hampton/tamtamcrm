<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{

    /**
     *
     * @param string $order
     * @param string $sort
     * @param Account $account
     * @param array $except
     * @return Collection
     */
    public function listCategories(
        string $order = 'id',
        string $sort = 'desc',
        Account $account,
        $except = []
    ): Collection;

    /**
     * @param array $params
     * @param Category $category
     * @return Category
     */
    public function createCategory(array $params, Category $category): Category;

    /**
     * @param array $params
     * @param Category $category
     * @return Category
     */
    public function updateCategory(array $params, Category $category): Category;

    /**
     *
     * @param int $id
     * @return Category
     * @return Category
     */
    public function findCategoryById(int $id): Category;

    /**
     *
     */
    public function deleteCategory(): bool;

    /**
     *
     * @param Product $product
     */
    public function associateProduct(Product $product);

    /**
     *
     */
    public function findProducts(): Collection;

    /**
     *
     * @param array $params
     */
    public function syncProducts(array $params);

    /**
     *
     */
    public function detachProducts();

    /**
     *
     * @param array $file
     * @param type $disk
     * @return bool
     * @return bool
     */
    public function deleteFile(array $file, $disk = null): bool;

    /**
     * @param string $slug
     * @param Account $account
     * @return Category
     */
    public function findCategoryBySlug(string $slug, Account $account): Category;

    /**
     *
     * @param string $string
     * @param string $string1
     */
    public function rootCategories(string $string, string $string1);
}
