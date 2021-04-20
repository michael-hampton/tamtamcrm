<?php

namespace App\Search;

use App\Models\Account;
use App\Models\CustomerGatewayToken;
use App\Models\Group;
use App\Repositories\CustomerGatewayTokenRepository;
use App\Repositories\GroupRepository;
use App\Requests\SearchRequest;
use App\Transformations\CustomerGatewayTokenTransformable;
use App\Transformations\GroupTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerGatewayTokenSearch extends BaseSearch
{
    use CustomerGatewayTokenTransformable;

    /**
     * @var CustomerGatewayTokenRepository
     */
    private CustomerGatewayTokenRepository $customer_gateway_token_repository;

    /**
     * @var CustomerGatewayToken
     */
    private CustomerGatewayToken $customer_gateway_token;

    /**
     * CustomerGatewayTokenSearch constructor.
     * @param CustomerGatewayTokenRepository $customer_gateway_token_repository
     */
    public function __construct(CustomerGatewayTokenRepository $customer_gateway_token_repository)
    {
        $this->customer_gateway_token_repository = $customer_gateway_token_repository;
        $this->model = $customer_gateway_token_repository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'created_at' : $request->column;
        $orderDir = !$request->order ? 'desc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $tokens = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->customer_gateway_token_repository->paginateArrayResults($tokens, $recordsPerPage);
            return $paginatedResults;
        }

        return $tokens;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $tokens = $list->map(
            function (CustomerGatewayToken $customer_gateway_token) {
                return $this->transformCustomerGatewayToken($customer_gateway_token);
            }
        )->all();

        return $tokens;
    }

}
