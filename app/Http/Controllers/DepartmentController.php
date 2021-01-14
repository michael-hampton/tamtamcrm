<?php

namespace App\Http\Controllers;

use App\Factory\DepartmentFactory;
use App\Models\Department;
use App\Repositories\DepartmentRepository;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Requests\CreateDepartmentRequest;
use App\Requests\SearchRequest;
use App\Requests\UpdateDepartmentRequest;
use App\Transformations\DepartmentTransformable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DepartmentController extends Controller
{

    use DepartmentTransformable;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $department_repo;

    /**
     * DepartmentController constructor.
     *
     * @param DepartmentRepositoryInterface $department_repo
     */
    public function __construct(DepartmentRepositoryInterface $department_repo)
    {
        $this->department_repo = $department_repo;
    }

    /**
     * @param SearchRequest $request
     * @return Factory|View
     */
    public function index(SearchRequest $request)
    {
        $orderBy = !$request->column ? 'name' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;

        if (request()->has('search_term') && !empty($request->search_term)) {
            $list = $this->department_repo->searchDepartment(request()->input('search_term'))->where(
                'account_id',
                auth()->user()->account_user()->account_id
            );
        } else {
            $list = $this->department_repo->listDepartments($orderBy, $orderDir)->where(
                'account_id',
                auth()->user()->account_user()->account_id
            );
        }

        $departments = $list->map(
            function (Department $department) {
                return $this->transformDepartment($department);
            }
        )->all();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->department_repo->paginateArrayResults($departments, $recordsPerPage);
            return $paginatedResults->toJson();
        }

        return collect($departments)->toJson();
    }

    /**
     * @param CreateDepartmentRequest $request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(CreateDepartmentRequest $request)
    {
        $department = $this->department_repo->save(
            $request->all(),
            DepartmentFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id)
        );
        return response()->json($this->transformDepartment($department));
    }

    /**
     * @param UpdateDepartmentRequest $request
     * @param $id
     *
     * @return Factory|View
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = $this->department_repo->findDepartmentById($id);

        if ($request->has('permissions')) {
            $departmentRepo = new DepartmentRepository($department);
            $departmentRepo->syncPermissions($request->input('permissions'));
        }

        $data = $request->except('_method', '_token', 'permissions');
        $department = $this->department_repo->save($data, $department);
        return response()->json($department);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        $department = $this->departmentRepo->findDepartmentById($id);

        $departmentRepo = new DepartmentRepository($department);
        $departmentRepo->deleteDepartment();
        return response()->json('Customer deleted!');
    }

}
