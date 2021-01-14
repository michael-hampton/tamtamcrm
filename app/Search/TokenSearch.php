<?php

namespace App\Search;

use App\Models\Account;
use App\Models\CompanyToken;
use App\Repositories\TokenRepository;
use App\Requests\SearchRequest;
use App\Transformations\TokenTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TokenSearch
 */
class TokenSearch extends BaseSearch
{
    use TokenTransformable;

    private TokenRepository $token_repo;

    private CompanyToken $model;

    /**
     * TokenSearch constructor.
     * @param TokenRepository $token_repo
     */
    public function __construct(TokenRepository $token_repo)
    {
        $this->token_repo = $token_repo;
        $this->model = $token_repo->getModel();
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

        $this->query = $this->model->select('company_tokens.*');

        if ($request->has('status')) {
            $this->status('company_tokens', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->query = $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $tokens = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->token_repo->paginateArrayResults($tokens, $recordsPerPage);
            return $paginatedResults;
        }

        return $tokens;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('company_tokens.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $tokens = $list->map(
            function (CompanyToken $company_token) {
                return $this->transform($company_token);
            }
        )->all();

        return $tokens;
    }
}
