<?php


namespace App\Factory;


use App\Models\Deal;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class CloneDealToTaskFactory
{
    /**
     * @param Deal $deal
     * @param User $user
     * @return Project|null
     */
    public static function create(Deal $deal, User $user): ?Task
    {
        $task = new Task();
        $task->name = $deal->name;
        $task->description = $deal->description;
        $task->due_date = $deal->due_date;
        $task->customer_id = $deal->customer_id;
        $task->assigned_to = $deal->assigned_to;
        $task->account_id = $deal->account_id;
        $task->user_id = $user->id;
        $task->private_notes = $deal->private_notes;
        $task->public_notes = $deal->public_notes;

        return $task;
    }
}