<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function view(User $user, Task $task)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $task->user_id === $user->id || $user->hasPermissionTo(
                'taskcontroller.show'
            ) || (!empty($task->assigned_to) && $task->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $task->user_id === $user->id || $user->hasPermissionTo(
                'taskcontroller.destroy'
            ) || (!empty($task->assigned_to) && $task->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $task->user_id === $user->id || $user->hasPermissionTo(
                'taskcontroller.update'
            ) || (!empty($task->assigned_to) && $task->assigned_to === $user->id);
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
                'taskcontroller.store'
            );
    }
}
