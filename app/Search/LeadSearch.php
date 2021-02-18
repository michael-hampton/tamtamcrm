<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Lead;
use App\Repositories\LeadRepository;
use App\Transformations\LeadTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
        } else {
            $this->query->withTrashed();
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

        $this->checkPermissions('leadcontroller.index');

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

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('leads');

        if (!empty($request->input('group_by'))) {
            // assigned to, status, source_type
            $this->query->select(
                DB::raw(
                    'count(*) as count, CONCAT(leads.first_name," ",leads.last_name) as lead_name, task_statuses.name AS status, source_type.name AS source_type, CONCAT(users.first_name," ",users.last_name) as assigned_to, SUM(valued_at) as valued_at'
                )
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select(
                'task_statuses.name AS status',
                'source_type.name AS source_type',
                'valued_at',
                'due_date',
                DB::raw('CONCAT(users.first_name," ",users.last_name) as assigned_to'),
                DB::raw('CONCAT(leads.first_name," ",leads.last_name) as lead_name')
            );
        }

        $order = $request->input('orderByField');

        $this->query->leftJoin('source_type', 'source_type.id', '=', 'leads.source_type')
                    ->join('task_statuses', 'task_statuses.id', '=', 'leads.task_status_id')
                    ->leftJoin('users', 'users.id', '=', 'leads.assigned_to')
                    ->where('leads.account_id', '=', $account->id);

        if ($order === 'status') {
            $this->query->orderBy('task_statuses.name', $request->input('orderByDirection'));
        } elseif ($order === 'lead_name') {
            $this->query->orderByRaw(
                'CONCAT(leads.first_name, " ", leads.last_name)' . $request->input('orderByDirection')
            );
        } elseif ($order === 'source_type') {
            $this->query->orderBy('source_type.name', $request->input('orderByDirection'));
        } else {
            $this->query->orderBy('leads.' . $order, $request->input('orderByDirection'));
        }
        //$this->query->where('status', '<>', 1)

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->lead_repo->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

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
