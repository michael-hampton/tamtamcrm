<?php


namespace App\Factory;


use App\Models\Deal;
use App\Models\Task;
use App\Models\User;

class CloneTaskToDealFactory
{
    /**
     * @param Task $task
     * @param User $user
     * @return Deal|null
     */
    public static function create(Task $task, User $user): ?Deal
    {
        $deal = new Deal();
        $deal->name = $task->name;
        $deal->description = $task->description;
        $deal->due_date = $task->due_date;
        $deal->customer_id = $task->customer_id;
        $deal->assigned_to = $task->assigned_to;
        $deal->account_id = $task->account_id;
        $deal->user_id = $user->id;
        $deal->private_notes = $task->private_notes;
        $deal->public_notes = $task->public_notes;

        return $deal;
    }
}