<?php

namespace App\Http\Controllers;

use App\Factory\CloneDealToLeadFactory;
use App\Factory\DealFactory;
use App\Factory\Lead\CloneLeadToTaskFactory;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\DealRepository;
use App\Repositories\LeadRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Requests\Deal\CreateDealRequest;
use App\Requests\Deal\UpdateDealRequest;
use App\Requests\SearchRequest;
use App\Search\DealSearch;
use App\Transformations\DealTransformable;
use App\Transformations\LeadTransformable;
use App\Transformations\TaskTransformable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DealController extends Controller
{

    use DealTransformable;
    use TaskTransformable;
    use LeadTransformable;

    /**
     * @var DealRepository
     */
    private $deal_repo;

    private $task_service;

    /**
     *
     * @param DealRepository $deal_repo
     */
    public function __construct(DealRepository $deal_repo)
    {
        $this->deal_repo = $deal_repo;
    }

    public function index(SearchRequest $request)
    {
        $deals = (new DealSearch($this->deal_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($deals);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateDealRequest $request
     * @return Response
     * @throws Exception
     */
    public function store(CreateDealRequest $request)
    {
        $deal = $this->deal_repo->createDeal(
            $request->all(),
            (new DealFactory)->create(auth()->user(), auth()->user()->account_user()->account)
        );

        //$task = SaveTaskTimes::dispatchNow($request->all(), $task);
        return response()->json($this->transformDeal($deal));
    }

    /**
     *
     * @param int $deal_id
     * @return type
     * @throws Exception
     */
    public function markAsCompleted(int $deal_id)
    {
        $objDeal = $this->deal_repo->findDealById(deal_id);
        $deal = $this->deal_repo->save(['is_completed' => true], $deal);
        return response()->json($deal);
    }


    /**
     * @param UpdateDealRequest $request
     * @param int $id
     *
     * @return Response
     * @throws Exception
     */
    public function update(UpdateDealRequest $request, int $id)
    {
        $deal = $this->deal_repo->findDealById($id);
        $deal = $this->deal_repo->updateDeal($request->all(), $deal);


        return response()->json($deal);
    }

    public function show(int $id)
    {
        $deal = $this->deal_repo->getDealById($id);
        return response()->json($this->transformDeal($deal));
    }


    /**
     * @param int $id
     *
     * @return void
     * @throws Exception
     */
    public function archive(int $id)
    {
        $deal = $this->deal_repo->findDealById($id);
        $deal->archive();
    }

    public function destroy(int $id)
    {
        $deal = $this->deal_repo->findDealById($id);

        $this->authorize('delete', $deal);

        $deal->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $deal = Deal::withTrashed()->where('id', '=', $id)->first();
        $deal->restoreEntity();
        return response()->json([], 200);
    }

    public function action(Request $request, Deal $deal, $action)
    {
        switch ($action) {
            case 'clone_to_task':
                $task = (new TaskRepository(new Task(), new ProjectRepository(new Project())))->save(
                    $request->all(),
                    CloneLeadToTaskFactory::create(
                        $deal,
                        auth()->user()
                    )
                );

                return response()->json($this->transformTask($task));

                break;

            case 'clone_to_lead':
                $lead = (new LeadRepository(new Lead))->save(
                    [],
                    CloneDealToLeadFactory::create($deal, auth()->user())
                );
                return response()->json($this->transformLead($lead));
                break;
            case 'download': //done
                $disk = config('filesystems.default');
                $content = Storage::disk($disk)->get($deal->service()->generatePdf(null));
                $response = ['data' => base64_encode($content)];
                return response()->json($response);
                break;
        }
    }

    public function sortTasks(Request $request)
    {
        foreach ($request->input('tasks') as $data) {
            $task = $this->deal_repo->findDealById($data['id']);

            $task->task_sort_order = $data['task_sort_order'];
            $task->save();
        }
    }
}
