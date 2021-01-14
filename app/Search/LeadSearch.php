<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Lead;
use App\Repositories\LeadRepository;
use App\Transformations\LeadTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadSearch extends BaseSearch
{
    use LeadTransformable;

    private LeadRepository $lead_repo;
    private Lead $model;

    /**
     * LeadSearch constructor.
     * @param LeadRepository $lead_repo
     */
    public function __construct(LeadRepository $lead_repo)
    {
        $this->lead_repo = $lead_repo;
        $this->model = $lead_repo->getModel();
    }

    /**
     * @param Request $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(Request $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'task_sort_order' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('leads', $request->status, 'task_status_id');
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $leads = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->lead_repo->paginateArrayResults($leads, $recordsPerPage);
            return $paginatedResults;
        }

        return $leads;
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
                $query->where('leads.name', 'like', '%' . $filter . '%')
                      ->orWhere('leads.first_name', 'like', '%' . $filter . '%')
                      ->orWhere('leads.last_name', 'like', '%' . $filter . '%');
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
        $leads = $list->map(
            function (Lead $lead) {
                return $this->transformLead($lead);
            }
        )->all();

        return $leads;
    }
}
