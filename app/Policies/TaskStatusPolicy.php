<?php

namespace App\Policies;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskStatusPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param TaskStatus $taskStatus
     * @return mixed
     */
    public function view(User $user, TaskStatus $taskStatus)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $taskStatus->user_id === $user->id || $user->hasPermissionTo(
                'taskstatuscontroller.show'
            ) || (!empty($taskStatus->assigned_to) && $taskStatus->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param TaskStatus $taskStatus
     * @return mixed
     */
    public function delete(User $user, TaskStatus $taskStatus)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $taskStatus->user_id === $user->id || $user->hasPermissionTo(
                'taskstatuscontroller.destroy'
            ) || (!empty($taskStatus->assigned_to) && $taskStatus->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param TaskStatus $taskStatus
     * @return mixed
     */
    public function update(User $user, TaskStatus $taskStatus)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $taskStatus->user_id === $user->id || $user->hasPermissionTo(
                'taskstatuscontroller.update'
            ) || (!empty($taskStatus->assigned_to) && $taskStatus->assigned_to === $user->id);
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
                'taskstatuscontroller.store'
            );
    }
}
