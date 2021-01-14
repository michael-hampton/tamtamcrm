<?php


namespace App\Factory;


use App\Models\Account;
use App\Models\TaskStatus;
use App\Models\User;

class TaskStatusFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return TaskStatus
     */
    public static function create(Account $account, User $user)
    {
        $task_status = new TaskStatus;
        $task_status->account_id = $account->id;
        $task_status->user_id = $user->id;

        return $task_status;
    }
}