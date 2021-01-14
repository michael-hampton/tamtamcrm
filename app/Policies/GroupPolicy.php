<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $group->user_id === $user->id || $user->hasPermissionTo(
                'groupcontroller.show'
            ) || (!empty($group->assigned_to) && $group->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $group->user_id === $user->id || $user->hasPermissionTo(
                'groupcontroller.update'
            ) || (!empty($group->assigned_to) && $group->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Group $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $group->user_id === $user->id || $user->hasPermissionTo(
                'groupcontroller.destroy'
            ) || (!empty($group->assigned_to) && $group->assigned_to === $user->id);
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
                'groupcontroller.store'
            );
    }
}
