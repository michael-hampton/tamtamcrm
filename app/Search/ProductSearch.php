<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Requests\SearchRequest;
use App\Transformations\ProductTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductSearch extends BaseSearch
{
    use ProductTransformable;

    private ProductRepository $productRepository;

    private Product $model;

    /**
     * ProductSearch constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->model = $productRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'name' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('products.id as id', 'products.*')
                                   ->leftJoin('category_product', 'products.id', '=', 'category_product.product_id');

        if ($request->has('status')) {
            $this->status('products', $request->status);
        }

        if ($request->filled('company_id')) {
            $this->query->where('company_id', '=', $request->company_id);
        }

        if ($request->filled('category_id')) {
            $this->query->where('category_id', '=', $request->category_id);
        }

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $this->query->groupBy('products.id');

        $products = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->productRepository->paginateArrayResults($products, $recordsPerPage);
            return $paginatedResults;
        }

        return $products;
    }

    /**
     * Filter based on search text
     *
     * @param string query filter
     * @return bool
     * @deprecated
     */
    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('products.sku', 'like', '%' . $filter . '%')
                      ->orWhere('products.name', 'like', '%' . $filter . '%')
                      ->orWhere('products.notes', 'like', '%' . $filter . '%')
                      ->orWhere('products.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('products.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('products.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('products.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $products = $list->map(
            function (Product $product) {
                return $this->transformProduct($product);
            }
        )->all();

        return $products;
    }
}
