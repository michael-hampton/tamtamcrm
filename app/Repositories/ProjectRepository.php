<?php

namespace App\Repositories;

use App\Events\Project\ProjectWasCreated;
use App\Events\Project\ProjectWasUpdated;
use App\Models\Account;
use App\Models\Project;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use App\Search\ProjectSearch;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as Support;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{

    /**
     * ProjectRepository constructor.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        parent::__construct($project);
        $this->model = $project;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return InvoiceSearch|LengthAwarePaginator
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new ProjectSearch($this))->filter($search_request, $account);
    }

    /**
     * @param int $id
     *
     * @return Project
     * @throws Exception
     */
    public function findProjectById(int $id): Project
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteProject(): bool
    {
        return $this->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listProjects($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Support
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchProject(string $text = null): Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchProject($text)->get();
    }

    /**
     * @param $data
     * @param Project $project
     * @return Project|null
     */
    public function save($data, Project $project): ?Project
    {
        $is_add = empty($project->id);
        $project->fill($data);
        $project->setNumber();
        $project->save();

        if (!$is_add) {
            event(new ProjectWasUpdated($project));
        } else {
            event(new ProjectWasCreated($project));
        }

        return $project;
    }
}
