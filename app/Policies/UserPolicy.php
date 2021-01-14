<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $model->user_id === $user->id || $user->hasPermissionTo('usercontroller.show');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $model->user_id === $user->id || $user->hasPermissionTo('usercontroller.destroy');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $model->user_id === $user->id || $user->hasPermissionTo('usercontroller.update');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $user->hasPermissionTo(
                'usercontroller.store'
            );
    }
}
