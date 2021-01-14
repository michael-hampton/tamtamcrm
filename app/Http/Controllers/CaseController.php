<?php

namespace App\Http\Controllers;


use App\Factory\CaseFactory;
use App\Factory\CloneCaseToProjectFactory;
use App\Jobs\Utils\UploadFile;
use App\Models\Cases;
use App\Models\CompanyToken;
use App\Models\Customer;
use App\Models\Project;
use App\Repositories\CaseRepository;
use App\Repositories\ProjectRepository;
use App\Requests\Cases\CreateCaseRequest;
use App\Requests\Cases\UpdateCaseRequest;
use App\Requests\SearchRequest;
use App\Search\CaseSearch;
use App\Transformations\CaseTransformable;
use App\Transformations\ProjectTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function request;

class CaseController extends Controller
{
    use CaseTransformable;
    use ProjectTransformable;

    /**
     * @var CaseRepository
     */
    private CaseRepository $case_repo;

    /**
     * CaseController constructor.
     * @param CaseRepository $case_repository
     */
    public function __construct(CaseRepository $case_repository)
    {
        $this->case_repo = $case_repository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $token_sent = request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;

        $cases = (new CaseSearch($this->case_repo))->filter(
            $request,
            $account
        );
        return response()->json($cases);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $case = $this->case_repo->findCaseById($id);
        return response()->json($this->transform($case));
    }

    /**
     * @param int $id
     * @param UpdateCaseRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateCaseRequest $request)
    {
        $case = $this->case_repo->findCaseById($id);
        $case = $this->case_repo->updateCase($request->all(), $case, auth()->user());
        return response()->json($this->transform($case->fresh()));
    }

    /**
     * @param CreateCaseRequest $request
     * @return JsonResponse
     */
    public function store(CreateCaseRequest $request)
    {
        $case = CaseFactory::create(
            auth()->user()->account_user()->account,
            auth()->user(),
            Customer::find($request->customer_id)->first()
        );

        $this->case_repo->createCase($request->all(), $case);

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $count => $file) {
                UploadFile::dispatchNow($file, $user, $account, $case);
            }
        }

        return response()->json($this->transform($case));
    }

    public function archive(int $id)
    {
        $case = $this->case_repo->findCaseById($id);
        $case->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $case = Cases::withTrashed()->where('id', '=', $id)->first();

        $this->authorize('delete', $case);

        $case->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $case = Cases::withTrashed()->where('id', '=', $id)->first();
        $case->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @return JsonResponse
     */
    public function bulk()
    {
        $action = request()->input('action');

        $ids = request()->input('ids');
        $cases = Cases::withTrashed()->find($ids);

        return response()->json($cases);
    }

    public function action(Request $request, Cases $case, $action)
    {
        switch ($action) {
            case 'clone_case_to_project':
                $project = (new ProjectRepository(new Project))->save(
                    $request->all(),
                    CloneCaseToProjectFactory::create(
                        $case,
                        auth()->user()
                    )
                );

                return response()->json($this->transformProject($project));

                break;

            case 'merge_case':
                if (empty($request->input('parent_id'))) {
                    return response()->json('You must select a parent');
                }
                $case = $case->service()->mergeCase($request, auth()->user());
                return response()->json($case);
                break;
            case 'download': //done
                $disk = config('filesystems.default');
                $content = Storage::disk($disk)->get($case->service()->generatePdf(null));
                $response = ['data' => base64_encode($content)];
                return response()->json($response);
                break;
        }

        return false;
    }
}
