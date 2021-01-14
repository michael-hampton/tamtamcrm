<?php

namespace App\Http\Controllers;

use App\Events\Lead\LeadWasCreated;
use App\Factory\Lead\CloneLeadToDealFactory;
use App\Factory\Lead\CloneLeadToTaskFactory;
use App\Factory\LeadFactory;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\DealRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\LeadRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Requests\Lead\CreateLeadRequest;
use App\Requests\Lead\UpdateLeadRequest;
use App\Requests\SearchRequest;
use App\Search\LeadSearch;
use App\Transformations\DealTransformable;
use App\Transformations\LeadTransformable;
use App\Transformations\TaskTransformable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
    use LeadTransformable;
    use DealTransformable;
    use TaskTransformable;

    /**
     * @var CustomerRepositoryInterface
     */
    private $lead_repo;

    /**
     * MessageController constructor.
     * @param LeadRepository $lead_repo
     */
    public function __construct(LeadRepository $lead_repo)
    {
        $this->lead_repo = $lead_repo;
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $leads = (new LeadSearch($this->lead_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($leads);
    }

    /**
     * @param CreateLeadRequest $request
     * @return JsonResponse
     */
    public function store(CreateLeadRequest $request)
    {
        $lead = $this->lead_repo->createLead(
            LeadFactory::create(auth()->user()->account_user()->account, auth()->user()),
            $request->all()
        );

        event(new LeadWasCreated($lead));
        return response()->json($this->transformLead($lead));
    }

    /**
     * @param int $id
     * @param UpdateLeadRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateLeadRequest $request)
    {
        $lead = $this->lead_repo->findLeadById($id);
        $lead = $this->lead_repo->updateLead($lead, $request->all());
        return response()->json($lead);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function convert(int $id)
    {
        $lead = $this->lead_repo->findLeadById($id);
        $lead = $lead->service()->convertLead();
        return response()->json($lead);
    }

    public function archive(int $id)
    {
        $lead = $this->lead_repo->findLeadById($id);
        $lead->archive();
    }

    public function destroy(int $id)
    {
        $lead = $this->lead_repo->findLeadById($id);

        $this->authorize('delete', $lead);

        $lead->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $lead = Lead::withTrashed()->where('id', '=', $id)->first();
        $lead->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @param Lead $lead
     * @param $action
     * @return JsonResponse
     * @throws Exception
     */
    public function action(Request $request, Lead $lead, $action)
    {
        switch ($action) {
            case 'clone_lead_to_task':
                $task = (new TaskRepository(new Task(), new ProjectRepository(new Project())))->save(
                    [],
                    CloneLeadToTaskFactory::create(
                        $lead,
                        auth()->user(),
                        auth()->user()->account_user()->account
                    )
                );

                return response()->json($this->transformTask($task));

                break;

            case 'clone_lead_to_deal':
                $deal = (new DealRepository(new Deal()))->save(
                    [],
                    CloneLeadToDealFactory::create($lead, auth()->user(), auth()->user()->account_user()->account),
                );
                return response()->json($this->transformDeal($deal));
                break;
            case 'download': //done
                $disk = config('filesystems.default');
                $content = Storage::disk($disk)->get($lead->service()->generatePdf(null));
                $response = ['data' => base64_encode($content)];

                return response()->json($response);
                break;
        }
    }

    public function sortTasks(Request $request)
    {
        foreach ($request->input('tasks') as $data) {
            $task = $this->lead_repo->findLeadById($data['id']);

            $task->task_sort_order = $data['task_sort_order'];
            $task->save();
        }
    }
}
