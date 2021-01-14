<?php

namespace App\Http\Controllers;

use App\Factory\ProjectFactory;
use App\Models\Customer;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Requests\Project\CreateProjectRequest;
use App\Requests\Project\UpdateProjectRequest;
use App\Search\ProjectSearch;
use App\Transformations\ProjectTransformable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ProjectTransformable;

    private $project_repo;

    /**
     *
     * @param ProjectRepositoryInterface $project_repo
     */
    public function __construct(ProjectRepositoryInterface $project_repo)
    {
        $this->project_repo = $project_repo;
    }

    public function index(Request $request)
    {
        $projects =
            (new ProjectSearch($this->project_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProjectRequest $request
     * @return Response
     */
    public function store(CreateProjectRequest $request)
    {
        $project = $this->project_repo->save(
            $request->all(),
            ProjectFactory::create(
                auth()->user(),
                Customer::where('id', $request->customer_id)->first(),
                auth()->user()->account_user()->account
            )
        );

        return response()->json($this->transformProject($project));
    }

    /**
     * @param UpdateProjectRequest $request
     * @param int $id
     *
     * @return Response
     */
    public function update(UpdateProjectRequest $request, int $id)
    {
        $project = $this->project_repo->findProjectById($id);
        $project_repo = new ProjectRepository($project);
        $project = $project_repo->save($request->all(), $project);
        return response()->json($this->transformProject($project));
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $project = $this->project_repo->findProjectById($id);
        return response()->json($this->transformProject($project));
    }

    public function markAsCompleted(Project $project)
    {
        $project->is_completed = true;
        $project->update();

        return response()->json('Project updated!');
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $project = Project::withTrashed()->where('id', '=', $id)->first();
        $project->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function archive(int $id)
    {
        $project = $this->project_repo->findProjectById($id);
        $project->archive();
    }

    public function destroy(int $id)
    {
        $project = Project::withTrashed()->where('id', '=', $id)->first();

        $this->authorize('delete', $project);
        $project->deleteEntity();
        return response()->json([], 200);
    }
}
