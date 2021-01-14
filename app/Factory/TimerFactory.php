<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Task;
use App\Models\Timer;
use App\Models\User;
use Carbon\Carbon;

class TimerFactory
{
    public static function create(User $user, Account $account, Task $task): Timer
    {
        $timer = new Timer;
        $timer->user_id = $user->id;
        $timer->task_id = $task->id;
        $timer->account_id = $account->id;
        $timer->started_at = new Carbon;

        return $timer;
    }
}
