<?php

namespace App\Repositories;

use App\Events\Task\TaskWasCreated;
use App\Events\Task\TaskWasUpdated;
use App\Models\Account;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\TaskSearch;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as Support;
use Illuminate\Support\Facades\DB;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    private $project_repo;

    /**
     * TaskRepository constructor.
     *
     * @param Task $task
     * @param ProjectRepository $project_repo
     */
    public function __construct(Task $task, ProjectRepository $project_repo)
    {
        parent::__construct($task);
        $this->model = $task;
        $this->project_repo = $project_repo;
    }

    /**
     * @param int $id
     *
     * @return Task
     * @throws Exception
     */
    public function findTaskById(int $id): Task
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteTask(): bool
    {
        $result = $this->delete();
        $this->model->products()->detach();
        return $result;
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new TaskSearch($this))->filter($search_request, $account);
    }

    /**
     *
     * @param Project $objProject
     * @param User $objUser
     * @return Support
     */
    public function getTasksForProject(Project $objProject, User $objUser = null): Support
    {
        $query = $this->model->select('tasks.*')->where('project_id', $objProject->id)->where('is_completed', 0)
                             ->where('parent_id', 0);

        if ($objUser !== null) {
            $query->join('task_user', 'tasks.id', '=', 'task_user.task_id')->where('task_user.user_id', $objUser->id);
        }


        return $query->get();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getTasksWithProducts(): Support
    {
        return $this->model->join('product_task', 'product_task.task_id', '=', 'tasks.id')->select('tasks.*')
                           ->groupBy('tasks.id')->get();
    }

    /**
     *
     * @param Task $objTask
     * @return Support
     */
    public function getSubtasks(Task $objTask): Support
    {
        return $this->model->where('parent_id', $objTask->id)->get();
    }

    /**
     *
     * @param int $task_type
     * @param $account_id
     * @return Support
     */
    public function getSourceTypeCounts(int $task_type, $account_id): Support
    {
        return $this->model->join('source_type', 'source_type.id', '=', 'tasks.source_type')
                           ->select('source_type.name', DB::raw('count(*) as value'))->where(
                'tasks.account_id',
                $account_id
            )->groupBy('source_type.name')->get();
    }

    /**
     *
     * @param int $account_id
     * @return Support
     */
    public function getStatusCounts(int $account_id): Support
    {
        return $this->model->join('task_statuses', 'task_statuses.id', '=', 'tasks.task_status_id')
                           ->select(
                               'task_statuses.name',
                               DB::raw('CEILING(count(*) * 100 / (select count(*) from tasks)) as value')
                           )
                           ->where('tasks.account_id', $account_id)
                           ->groupBy('task_statuses.name')->get();
    }

    /**
     *
     * @param int $number_of_days
     * @param int $account_id
     * @return type
     */
    public function getRecentTasks(int $number_of_days, int $account_id)
    {
        $date = Carbon::today()->subDays($number_of_days);
        $result = $this->model->select(DB::raw('count(*) as total'))->where('created_at', '>=', $date)
                              ->where('account_id', $account_id)->get();

        return !empty($result[0]) ? $result[0]['total'] : 0;
    }

    public function createTask($data, Task $task): ?Task
    {
        if (empty($data['customer_id']) && !empty($data['project_id'])) {
            $project = $this->project_repo->findProjectById($data['project_id']);

            $data['customer_id'] = $project->customer_id;
        }

        $data['source_type'] = empty($data['source_type']) ? 1 : $data['source_type'];

        $task = $this->save($data, $task);

        event(new TaskWasCreated($task));

        return $task;
    }


    /**
     * @param array $data
     * @param Task $task
     * @return Task|null
     */
    public function save(array $data, Task $task): ?Task
    {
        $task->setNumber();

        $task->fill($data);
        $task->save();

        if (isset ($data['contributors']) && !empty($data['contributors'])) {
            $this->syncUsers($task, $data['contributors']);
        }

        return $task->fresh();
    }

    /**
     * Sync the users
     *
     * @param Task $task
     * @param array $params
     * @return array
     */
    public function syncUsers(Task $task, array $params)
    {
        return $task->users()->sync($params);
    }

    /**
     * @param $data
     * @param Task $task
     * @return Task|null
     * @throws Exception
     */
    public function updateTask($data, Task $task): ?Task
    {
        $task = $this->save($data, $task);

        event(new TaskWasUpdated($task));

        return $task->fresh();
    }

    private function saveProjectTask($data, Task $task)
    {
        $objProject = (new ProjectRepository(new Project))->findProjectById($data['project_id']);
        $data['customer_id'] = $objProject->customer_id;
        $task->fill($data);

        $task->save();
        $objProject->tasks()->attach($task);

        if (isset ($data['contributors']) && !empty($data['contributors'])) {
            $this->syncUsers($task, $data['contributors']);
        }

        return $task->fresh();
    }

}
