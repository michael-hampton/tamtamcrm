<?php


namespace App\Factory;


use App\Models\Cases;
use App\Models\Project;
use App\Models\User;

class CloneCaseToProjectFactory
{
    /**
     * @param Cases $case
     * @param User $user
     * @return Project|null
     */
    public static function create(Cases $case, User $user): ?Project
    {
        $project = new Project();
        $project->name = $case->subject;
        $project->description = $case->message;
        $project->due_date = $case->due_date;
        $project->customer_id = $case->customer_id;
        $project->assigned_to = $case->assigned_to;
        $project->account_id = $case->account_id;
        $project->user_id = $user->id;
        $project->private_notes = $case->private_notes;

        return $project;
    }
}