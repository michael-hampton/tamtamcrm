<?php

namespace App\Http\Controllers;

use App\Factory\TaskStatusFactory;
use App\Models\CompanyToken;
use App\Repositories\Interfaces\TaskStatusRepositoryInterface;
use App\Repositories\TaskStatusRepository;
use App\Requests\SearchRequest;
use App\Requests\TaskStatus\CreateTaskStatusRequest;
use App\Requests\TaskStatus\UpdateTaskStatusRequest;
use App\Search\TaskStatusSearch;
use App\Transformations\TaskStatusTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

use function request;

class TaskStatusController extends Controller
{

    use TaskStatusTransformable;

    private $task_status_repo;

    public function __construct(TaskStatusRepositoryInterface $task_status_repo)
    {
        $this->task_status_repo = $task_status_repo;
    }

    public function index(SearchRequest $request)
    {
        $token_sent = request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;

        $statuses = (new TaskStatusSearch($this->task_status_repo))->filter(
            $request,
            $account
        );
        return response()->json($statuses);
    }

    /**
     * @param CreateTaskStatusRequest $request
     * @return JsonResponse
     */
    public function store(CreateTaskStatusRequest $request)
    {
        $status = $this->task_status_repo->save(
            $request->all(),
            TaskStatusFactory::create(auth()->user()->account_user()->account, auth()->user())
        );

        return response()->json($this->transformTaskStatus($status));
    }

    /**
     * @param UpdateTaskStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTaskStatusRequest $request, int $id)
    {
        $status = $this->task_status_repo->findTaskStatusById($id);
        $update = new TaskStatusRepository($status);
        $status = $update->save($request->all(), $status);
        return response()->json($this->transformTaskStatus($status));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $task_status = $this->task_status_repo->findTaskStatusById($id);
        $this->authorize('delete', $task_status);
        $task_status->delete();
    }

}
