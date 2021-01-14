<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Task;
use App\Models\User;

class TaskFactory
{
    public static function create(User $user, Account $account): Task
    {
        $task = new Task;
        $task->source_type = 1;
        $task->user_id = $user->id;
        $task->created_by = $user->id;
        $task->account_id = $account->id;

        return $task;
    }
}
