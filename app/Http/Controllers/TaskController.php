<?php

namespace App\Http\Controllers;

use App\Factory\CloneTaskToDealFactory;
use App\Factory\TaskFactory;
use App\Factory\TimerFactory;
use App\Jobs\Order\CreateOrder;
use App\Jobs\Pdf\Download;
use App\Jobs\Task\GenerateInvoice;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Project;
use App\Models\SourceType;
use App\Models\Task;
use App\Models\Timer;
use App\Repositories\CustomerRepository;
use App\Repositories\DealRepository;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SourceTypeRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TimerRepository;
use App\Requests\Order\CreateOrderRequest;
use App\Requests\SearchRequest;
use App\Requests\Task\CreateDealRequest;
use App\Requests\Task\CreateTaskRequest;
use App\Requests\Task\UpdateTaskRequest;
use App\Search\TaskSearch;
use App\Transformations\DealTransformable;
use App\Transformations\TaskTransformable;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ReflectionException;

class TaskController extends Controller
{

    use TaskTransformable;
    use DealTransformable;

    /**
     * @var TaskRepositoryInterface
     */
    private $task_repo;

    /**
     * @var ProjectRepositoryInterface
     */
    private $project_repo;

    private $task_service;

    /**
     *
     * @param TaskRepositoryInterface $task_repo
     * @param ProjectRepositoryInterface $project_repo
     */
    public function __construct(TaskRepositoryInterface $task_repo, ProjectRepositoryInterface $project_repo)
    {
        $this->task_repo = $task_repo;
        $this->project_repo = $project_repo;
    }

    public function index(SearchRequest $request)
    {
        $tasks = (new TaskSearch($this->task_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     * @param CreateTaskRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CreateTaskRequest $request)
    {
        $task = $this->task_repo->createTask(
            $request->all(),
            (new TaskFactory)->create(auth()->user(), auth()->user()->account_user()->account)
        );

        if (!empty($request->input('timers'))) {
            $task->service()->saveTimers($request->input('timers'), $task, new TimerRepository(new Timer()));
        }

        if ($task->customer->getSetting('task_automation_enabled') === true && empty($request->input('timers'))) {
            return $this->timerAction('start_timer', $task);
        }

        return response()->json($this->transformTask($task));
    }

    private function timerAction($action, Task $task)
    {
        $timer_repo = new TimerRepository(new Timer());

        if ($action === 'stop_timer') {
            $timer_repo->stopTimer($task);
        }

        if ($action === 'resume_timer' || $action === 'start_timer') {
            $timer = TimerFactory::create(auth()->user(), auth()->user()->account_user()->account, $task);
            $timer_repo->startTimer($timer, $task);
        }

        return response()->json($this->transformTask($task->fresh()));
    }

    /**
     *
     * @param int $task_id
     * @return type
     */
    public function markAsCompleted(int $task_id)
    {
        $task = $this->task_repo->findTaskById($task_id);
        $task = $this->task_repo->save(['is_completed' => true], $task);
        return response()->json($task);
    }

    /**
     *
     * @param int $projectId
     * @return type
     */
    public function getTasksForProject(int $projectId)
    {
        $objProject = $this->project_repo->findProjectById($projectId);
        $list = $this->task_repo->getTasksForProject($objProject);

        $tasks = $list->map(
            function (Task $task) {
                return $this->transformTask($task);
            }
        )->all();

        return response()->json($tasks);
    }

    /**
     * @param UpdateTaskRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, int $id)
    {
        $task = $this->task_repo->findTaskById($id);
        $task = $this->task_repo->updateTask($request->all(), $task);

        if (!empty($request->input('timers'))) {
            $task->service()->saveTimers($request->input('timers'), $task, (new TimerRepository(new Timer())));
        }

        //$task = SaveTaskTimes::dispatchNow($request->all(), $task);
        return response()->json($task);
    }

    public function getDeals()
    {
        $list = $this->task_repo->getDeals();

        $tasks = $list->map(
            function (Task $task) {
                return $this->transformTask($task);
            }
        )->all();

        return response()->json($tasks);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function updateStatus(Request $request, int $id)
    {
        $task = $this->task_repo->findTaskById($id);
        $task = $this->task_repo->save(['task_status_id' => $request->task_status], $task);
        return response()->json($task);
    }

    /**
     * @param Request $request
     * @param int $task_type
     * @return mixed
     */
    public function filterTasks(Request $request, int $task_type)
    {
        $tasks = (new TaskSearch($this->task_repo))->filterBySearchCriteria(
            $request->all(),
            $task_type,
            auth()->user()->account_user()->account_id
        );
        return response()->json($tasks);
    }

    public function getTasksWithProducts()
    {
        $tasks = $this->task_repo->getTasksWithProducts();
        return $tasks->toJson();
    }

    /**
     *
     * @param int $task_id
     * @return type
     */
    public function getProducts(int $task_id)
    {
        $products = (new ProductRepository(new Product))->getAll(
            new SearchRequest,
            auth()->user()->account_user()->account
        );
        $task = $this->task_repo->findTaskById($task_id);
        $product_tasks = (new OrderRepository(new Order))->getOrdersForTask($task);

        $arrData = [
            'products'    => $products,
            'selectedIds' => $product_tasks->pluck('product_id')->all(),
        ];

        return response()->json($arrData);
    }

    /**
     *
     * @param CreateOrderRequest $request
     * @return type
     */
    public function createDeal(CreateOrderRequest $request)
    {
        $order = CreateOrder::dispatchNow(
            auth()->user()->account_user()->account,
            auth()->user(),
            $request,
            (new CustomerRepository(new Customer)),
            new OrderRepository(new Order),
            new TaskRepository(new Task, new ProjectRepository(new Project)),
            true
        );

        return response()->json($order);
    }

    /**
     *
     * @param int $parent_id
     * @return type
     */
    public function getSubtasks(int $parent_id)
    {
        $task = $this->task_repo->findTaskById($parent_id);
        $subtasks = $this->task_repo->getSubtasks($task);

        $tasks = $subtasks->map(
            function (Task $task) {
                return $this->transformTask($task);
            }
        )->all();
        return response()->json($tasks);
    }

    public function getSourceTypes()
    {
        $source_types = (new SourceTypeRepository(new SourceType))->getAll();
        return response()->json($source_types);
    }

    public function getTaskTypes()
    {
        $task_types = (new TaskTypeRepository(new TaskType))->getAll();
        return response()->json($task_types);
    }

    /**
     *
     * @param int $task_id
     * @return type
     */
    public function convertToDeal(int $task_id)
    {
        return response()->json('Unable to convert');
    }

    public function show(int $id)
    {
        $task = $this->task_repo->findTaskById($id);
        return response()->json($this->transformTask($task));
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function archive(int $id)
    {
        $task = $this->task_repo->findTaskById($id);
        $task->archive();
    }

    public function destroy(int $id)
    {
        $task = $this->task_repo->findTaskById($id);
        $this->authorize('delete', $task);
        $task->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $task = Task::withTrashed()->where('id', '=', $id)->first();
        $task->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @param Task $task
     * @param $action
     * @return JsonResponse
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function action(Request $request, Task $task, $action)
    {
        switch ($action) {
            case 'clone_task_to_deal':
                $deal = (new DealRepository(new Deal()))->save(
                    [],
                    CloneTaskToDealFactory::create($task, auth()->user())
                );
                return response()->json($this->transformDeal($deal));

            case 'download': //done
                $disk = config('filesystems.default');
                $content = Storage::disk($disk)->get($task->service()->generatePdf(null));
                $response = ['data' => base64_encode($content)];
                return response()->json($response);
                break;
            case 'stop_timer':
            case 'resume_timer':
            case 'start_timer':
                return $this->timerAction($action, $task);
                break;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function bulk(Request $request)
    {
        $action = $request->action;

        $ids = $request->ids;

        $tasks = Task::withTrashed()->whereIn('id', $ids)->get();

        if (!$tasks) {
            return response()->json(['message' => "No task Found"]);
        }

        if ($action == 'download' && $tasks->count() >= 1) {
            Download::dispatch($tasks, $tasks->first()->account, auth()->user()->email);

            return response()->json(['message' => 'The email was sent successfully!'], 200);
        }

        if ($action === 'create_invoice') {
            GenerateInvoice::dispatchNow(new InvoiceRepository(new Invoice), $tasks);
            return response()->json(['message' => 'The invoice was created successfully!'], 200);
        }

        $responses = [];

        foreach ($tasks as $task) {
            if ($action === 'mark_in_progress') {
                $task->setStatus(Task::STATUS_IN_PROGRESS);
                $task->save();
                return response()->json($this->transformTask($task->fresh()));
            } else {
                $response = $this->performAction($request, $task, $action, true);
            }

            if ($response === false) {
                $responses[] = "FAILED";
                continue;
            }

            $responses[] = $response;
        }

        return response()->json($responses);
    }

    public function sortTasks(Request $request)
    {
        foreach ($request->input('tasks') as $data) {
            $task = $this->task_repo->findTaskById($data['id']);

            $task->task_sort_order = $data['task_sort_order'];
            $task->save();
        }
    }
}
