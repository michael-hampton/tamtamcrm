<?php

namespace App\Search;

use App\Models\Account;
use App\Models\File;
use App\Repositories\FileRepository;
use App\Repositories\TaskRepository;
use App\Requests\SearchRequest;
use App\Transformations\FileTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DocumentSearch extends BaseSearch
{
    private FileRepository $file_repository;

    private File $model;

    /**
     * TaskSearch constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(FileRepository $file_repository)
    {
        $this->file_repository = $file_repository;
        $this->model = $file_repository->getModel();
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

        $this->query =
            $this->model->select('*');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->filled('user_id')) {
            $this->query->where('user_id', '=', $request->user_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        //$this->checkPermissions('taskcontroller.index', 'tasks');

        $this->orderBy($orderBy, $orderDir);

        $this->query->groupBy('tasks.id');

        $tasks = $this->transformList();

        if ($recordsPerPage > 0) {
            return $this->file_repository->paginateArrayResults($tasks, $recordsPerPage);
        }

        return $tasks;
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

        $this->query->where('name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        return $list->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('files');

        if (!empty($request->input('group_by'))) {
            // assigned to, status, customer, project
            $this->query->select(
                DB::raw(
                    'count(*) as count, files.fileable_type AS record_type, files.type AS file_type'
                )
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select(
                'files.name AS name',
                'files.type AS file_type',
                'files.fileable_type AS record_type',
                'files.size',
                'files.width',
                'files.height',
                DB::raw('DATE(created_at) AS created_at')
            );
        }

        $this->query->where('files.account_id', '=', $account->id);

        $order = $request->input('orderByField');

        if ($order === 'record_type') {
            $order = 'fileable_type';
        }

        if ($order === 'file_type') {
            $order = 'type';
        }

        $this->query->orderBy('files.' . $order, $request->input('orderByDirection'));

        if (!empty($request->input('date_format'))) {
            $this->filterByDate($request->input('date_format'), 'tasks');
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request, 'tasks', 'due_date');
        }

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->file_repository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
    }

}
