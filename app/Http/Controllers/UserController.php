<?php

namespace App\Http\Controllers;

use App\Events\User\UserWasCreated;
use App\Factory\UserFactory;
use App\Jobs\User\CreateUser;
use App\Models\Department;
use App\Models\User;
use App\Repositories\DepartmentRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Requests\SearchRequest;
use App\Requests\User\CreateUserRequest;
use App\Requests\User\DeleteUserRequest;
use App\Requests\User\UpdateUserRequest;
use App\Search\UserSearch;
use App\Transformations\UserTransformable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    use UserTransformable;

    /**
     * @var UserRepositoryInterface
     */
    private $user_repo;

    /**
     * @var RoleRepositoryInterface
     */
    private $role_repo;

    /**
     * UserController constructor.
     * @param UserRepositoryInterface $user_repo
     * @param RoleRepositoryInterface $role_repo
     */
    public function __construct(UserRepositoryInterface $user_repo, RoleRepositoryInterface $role_repo)
    {
        $this->user_repo = $user_repo;
        $this->role_repo = $role_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $users = (new UserSearch($this->user_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($users);
    }

    public function dashboard()
    {
        return view('index');
    }

    /**
     * @param CreateUserRequest $request
     * @return array
     */
    public function store(CreateUserRequest $request)
    {
        $user = $this->user_repo->save(
            $request->except('customized_permissions'),
            UserFactory::create(auth()->user()->account_user()->account->domains->id)
        );

        $account_user = $user->account_users->where('account_id', $request->input('account_id'))->first();

        if (!empty($request->input('customized_permissions'))) {
            $this->user_repo->savePermissions(
                $user,
                $account_user,
                $request->input('customized_permissions')
            );
        }

        event(new UserWasCreated($user));

        return $this->transformUser($user);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function edit(User $user)
    {
        $roles = $this->role_repo->listRoles('created_at', 'desc')->where(
            'account_id',
            auth()->user()->account_user()->account_id
        );
        $arrData = [
            'user'        => $this->transformUser($user),
            'roles'       => $roles,
            'selectedIds' => $user->roles()->pluck('role_id')->all()
        ];

        return response()->json($arrData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function archive(User $user)
    {
        $response = $user->delete();

        if ($response) {
            return response()->json('User deleted!');
        }

        return response()->json('User could not be deleted!');
    }

    /**
     * @param DeleteUserRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        $this->authorize('delete', $user);
        $this->user_repo->deleteUser($user);
        return response()->json([], 200);
    }

    /**
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = $this->user_repo->save($request->except('customized_permissions'), $user);
        $account_user = $user->account_users->where('account_id', $request->input('account_id'))->first();

        if (!empty($request->input('customized_permissions'))) {
            $this->user_repo->savePermissions(
                $user,
                $account_user,
                $request->input('customized_permissions')
            );
        } else {
            DB::table('permission_user')->where('user_id', $user->id)->where(
                'account_id',
                $account_user->account->id
            )->delete();
        }

        return response()->json($user);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file') instanceof UploadedFile) {
            $user = auth()->user();
            $userRepo = new UserRepository($user);
            $data['profile_photo'] = $this->user_repo->saveUserImage($request->file('file'));
            $userRepo->updateUser($data);
        }

        return response()->json('file uploaded successfully');
    }

    /**
     * @param string $username
     * @return JsonResponse
     */
    public function profile(string $username)
    {
        $user = $this->user_repo->findUserByUsername($username);
        return response()->json($user);
    }

    /**
     * @param int $department_id
     * @return mixed
     */
    public function filterUsersByDepartment(int $department_id)
    {
        $objDepartment = (new DepartmentRepository(new Department))->findDepartmentById($department_id);
        $users = $this->user_repo->getUsersForDepartment($objDepartment);
        return response()->json($users);
    }

    /**
     * @return void
     */
    public function bulk()
    {
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(User $user)
    {
        return response()->json($this->transformUser($user));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $group = User::withTrashed()->where('id', '=', $id)->first();
        $this->user_repo->restore($group);
        return response()->json([], 200);
    }
}
