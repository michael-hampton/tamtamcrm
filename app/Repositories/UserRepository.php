<?php

namespace App\Repositories;

use App\Events\User\UserEmailChanged;
use App\Events\User\UserWasDeleted;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\Department;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection as Support;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->model = $user;
    }

    /**
     * @param int $id
     *
     * @return User
     * @throws Exception
     */
    public function findUserById(int $id): User
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteUser(): bool
    {
        return $this->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listUsers($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Support
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function getActiveUsers($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return User::where('is_active', 1)->orderBy($orderBy, $sortBy)->get();
    }

    /**
     *
     * @param string $username
     * @return User
     */
    public function findUserByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     *
     * @param Department $objDepartment
     * @return Support
     */
    public function getUsersForDepartment(Department $objDepartment): Support
    {
        return $this->model->join('department_user', 'department_user.user_id', '=', 'users.id')->select('users.*')
                           ->where('department_user.department_id', $objDepartment->id)->groupBy('users.id')->get();
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveUserImage(UploadedFile $file): string
    {
        return $file->store('users', ['disk' => 'public']);
    }

    /**
     * @param array $data
     * @param User $user
     * @return User|null
     */
    public function save(array $data, User $user): ?User
    {
        $data['username'] = !isset($data['username']) || empty($data['username']) && !empty($data['email']) ? $data['email'] : $data['username'];
        $email_changed = false;

        if (!empty($user->email) && $user->email !== $data['email']) {
            $email_changed = true;
        }

        /*************** save new user ***************************/
        $user->fill($data);

        if (isset($data['password']) && !empty($data['password']) && (empty($user->id) || auth()->user(
                )->id === $user->id)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if (isset($data['role']) && !empty($data['role'])) {
            $this->syncRoles($user, [$data['role']]);
        }

        if (isset($data['department']) && !empty($data['department'])) {
            $this->syncDepartment($user, $data['department']);
        }

        if (isset($data['company_user'])) {
            $account = Account::find(auth()->user()->account_user()->account_id);

            $cu = AccountUser::whereUserId($user->id)->whereAccountId($account->id)->withTrashed()->first();

            /*No company user exists - attach the user*/
            if (!$cu) {
                $user->attachUserToAccount(
                    $account,
                    $data['company_user']['is_admin'],
                    !empty($data['company_user']['notifications']) ? $data['company_user']['notifications'] : []
                );
            } else {
                $data['company_user']['notifications'] = !empty($data['company_user']['notifications']) ? $data['company_user']['notifications']
                    : $user->notificationDefaults();
                $cu->fill($data['company_user']);
                $cu->restore();
                $cu->save();
            }

            if ($email_changed === true) {
                event(new UserEmailChanged($user));
            }
        }

        return $user->fresh();
    }

    /**
     * @param User $user
     * @param array $roleIds
     * @return array
     */
    public function syncRoles(User $user, array $roleIds)
    {
        $mappedObjects = [];

        foreach ($roleIds[0] as $roleId) {
            $mappedObjects[] = $roleId;
        }

        return $user->roles()->sync($mappedObjects);
    }

    /**
     * Sync the categories
     *
     * @param User $user
     * @param int $department_id
     * @return array
     */
    public function syncDepartment(User $user, int $department_id)
    {
        return $user->departments()->sync($department_id);
    }

    /**
     * @param User $user
     * @param bool $delete_account
     * @return User|null
     * @throws Exception
     */
    public function destroy(User $user, $delete_account = false)
    {
        if ($delete_account === true) {
            $this->deleteUserAccount($user);
        }

        event(new UserWasDeleted($user));

        $user->delete();

        return $user->fresh();
    }

    private function deleteUserAccount(User $user)
    {
        $company = $user->account_user()->account;
        $company->forceDelete();

        return true;
    }
}
