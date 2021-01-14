<?php

namespace App\Search;

use App\Models\Account;
use App\Models\BankAccount;
use App\Repositories\BankAccountRepository;
use App\Requests\SearchRequest;
use App\Transformations\BankAccountTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class BankAccountSearch
 * @package App\Search
 */
class BankAccountSearch extends BaseSearch
{
    use BankAccountTransformable;

    /**
     * @var BankAccountRepository
     */
    private BankAccountRepository $bank_account_repo;

    private BankAccount $model;

    /**
     * BankAccountSearch constructor.
     * @param BankAccountRepository $bank_account_repo
     */
    public function __construct(BankAccountRepository $bank_account_repo)
    {
        $this->bank_account_repo = $bank_account_repo;
        $this->model = $bank_account_repo->getModel();
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
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('bank_accounts.*');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $bank_accounts = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->bank_account_repo->paginateArrayResults($bank_accounts, $recordsPerPage);
            return $paginatedResults;
        }

        return $bank_accounts;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('bank_accounts.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $bank_accounts = $list->map(
            function (BankAccount $bank_account) {
                return $this->transformBankAccount($bank_account);
            }
        )->all();

        return $bank_accounts;
    }
}
