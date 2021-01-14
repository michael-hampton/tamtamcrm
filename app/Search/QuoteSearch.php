<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Quote;
use App\Repositories\QuoteRepository;
use App\Requests\SearchRequest;
use App\Transformations\QuoteTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class QuoteSearch extends BaseSearch
{
    private QuoteRepository $quoteRepository;

    private Quote $model;

    /**
     * QuoteSearch constructor.
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
        $this->model = $quoteRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'due_date' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('quotes', $request->status);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $quotes = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->quoteRepository->paginateArrayResults($quotes, $recordsPerPage);
            return $paginatedResults;
        }

        return $quotes;
    }

    /**
     * @param string $filter
     * @return bool
     */
    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('quotes.number', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('quotes.custom_value4', 'like', '%' . $filter . '%');
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
        $quotes = $list->map(
            function (Quote $quote) {
                return (new QuoteTransformable())->transformQuote($quote);
            }
        )->all();

        return $quotes;
    }
}
