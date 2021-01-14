<?php

namespace App\Search;

use App\Models\Account;
use App\Models\RecurringQuote;
use App\Repositories\RecurringQuoteRepository;
use App\Requests\SearchRequest;
use App\Transformations\RecurringQuoteTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class RecurringQuoteSearch extends BaseSearch
{
    use RecurringQuoteTransformable;

    private RecurringQuoteRepository $recurringQuoteRepository;

    private RecurringQuote $model;

    /**
     * RecurringQuoteSearch constructor.
     * @param RecurringQuoteRepository $recurringQuoteRepository
     */
    public function __construct(RecurringQuoteRepository $recurringQuoteRepository)
    {
        $this->recurringQuoteRepository = $recurringQuoteRepository;
        $this->model = $recurringQuoteRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'due_date' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status')) {
            $this->status('recurring_quotes', $request->status);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $quotes = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->recurringQuoteRepository->paginateArrayResults($quotes, $recordsPerPage);
            return $paginatedResults;
        }

        return $quotes;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('recurring_quotes.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('recurring_quotes.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('recurring_quotes.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('recurring_quotes.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    private function transformList()
    {
        $list = $this->query->get();

        $quotes = $list->map(
            function (RecurringQuote $quote) {
                return $this->transformRecurringQuote($quote);
            }
        )->all();

        return $quotes;
    }

}
