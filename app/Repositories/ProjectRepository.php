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
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @param array $data
     * @param Project $project
     * @return Project
     */
    public function create(array $data, Project $project): Project
    {
        $project->fill($data);
        $project->setNumber();
        $project->save();

        event(new ProjectWasCreated($project));

        return $project;
    }

    /**
     * @param array $data
     * @param Project $project
     * @return Project
     */
    public function update(array $data, Project $project): Project
    {
        $project->update($data);

        event(new ProjectWasUpdated($project));

        return $project;
    }
}
