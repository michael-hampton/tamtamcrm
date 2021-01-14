<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use App\Transformations\ProjectTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectSearch extends BaseSearch
{
    use ProjectTransformable;

    private ProjectRepository $projectRepository;
    private Project $model;

    /**
     * ProjectSearch constructor.
     * @param ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->model = $projectRepository->getModel();
    }

    /**
     * @param Request $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(Request $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'name' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('projects', $request->status);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $projects = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->projectRepository->paginateArrayResults($projects, $recordsPerPage);
            return $paginatedResults;
        }

        return $projects;
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
                $query->where('projects.name', 'like', '%' . $filter . '%')
                      ->orWhere('projects.number', 'like', '%' . $filter . '%')
                      ->orWhere('projects.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('projects.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('projects.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('projects.custom_value4', 'like', '%' . $filter . '%')
                      ->orWhere('projects.private_notes', 'like', '%' . $filter . '%');
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
        $projects = $list->map(
            function (Project $project) {
                return $this->transformProject($project);
            }
        )->all();

        return $projects;
    }
}
