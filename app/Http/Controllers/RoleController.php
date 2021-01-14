<?php

namespace App\Http\Controllers;

use App\Factory\RoleFactory;
use App\Models\Role;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Requests\CreateRoleRequest;
use App\Requests\SearchRequest;
use App\Requests\UpdateRoleRequest;
use App\Transformations\RoleTransformable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RoleController extends Controller
{

    use RoleTransformable;

    /**
     * @var RoleRepositoryInterface
     */
    private $role_repo;

    /**
     * @var PermissionRepositoryInterface
     */
    private $permission_repo;

    /**
     * RoleController constructor.
     *
     * @param RoleRepositoryInterface $role_repo
     * @param PermissionRepositoryInterface $permission_repo
     */
    public function __construct(
        RoleRepositoryInterface $role_repo,
        PermissionRepositoryInterface $permission_repo
    ) {
        $this->role_repo = $role_repo;
        $this->permission_repo = $permission_repo;
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
            $list = $this->role_repo->searchRole(request()->input('search_term'))->where(
                'account_id',
                auth()->user()->account_user()->account_id
            );
        } else {
            $list = $this->role_repo->listRoles($orderBy, $orderDir)->where(
                'account_id',
                auth()->user()->account_user()->account_id
            );
        }

        $roles = $list->map(
            function (Role $role) {
                return $this->transformRole($role);
            }
        )->all();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->role_repo->paginateArrayResults($roles, $recordsPerPage);
            return $paginatedResults->toJson();
        }

        return collect($roles)->toJson();
    }

    /**
     * @param CreateRoleRequest $request
     *
     * @return RedirectResponse
     */
    public function store(CreateRoleRequest $request)
    {
        $role = $this->role_repo->save(
            $request->all(),
            RoleFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id)
        );

        if ($request->has('permissions')) {
            $roleRepo = new RoleRepository($role);
            $roleRepo->syncPermissions($request->input('permissions'));
        }

        return response()->json($this->transformRole($role));
    }

    /**
     * @param $id
     *
     * @return Factory|View
     */
    public function edit($id)
    {
        $role = $this->role_repo->findRoleById($id);
        $roleRepo = new RoleRepository($role);
        $attachedPermissionsArrayIds = $roleRepo->listPermissions()->pluck('id')->all();
        $permissions = $this->permission_repo->listPermissions(['id', 'name'], 'name', 'asc');

        $arrData = [
            'permissions'         => $permissions->toArray(),
            'role'                => $role->toArray(),
            'attachedPermissions' => $attachedPermissionsArrayIds
        ];

        return response()->json($arrData);
    }

    /**
     * @param UpdateRoleRequest $request
     * @param $id
     *
     * @return Factory|View
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->role_repo->findRoleById($id);

        if ($request->has('permissions')) {
            $roleRepo = new RoleRepository($role);
            $roleRepo->syncPermissions($request->input('permissions'));
        }

        $data = $request->except('_method', '_token', 'permissions');
        $this->role_repo->save($data, $role);
        return response()->json($this->transformRole($role));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(int $id)
    {
        $role = $this->roleRepo->findRoleById($id);

        $roleRepo = new RoleRepository($role);
        $roleRepo->deleteRoleById();
        return response()->json('Customer deleted!');
    }

}
