<?php


namespace App\Factory;


use App\Models\Deal;
use App\Models\Project;
use App\Models\User;

class CloneDealToProjectFactory
{
    /**
     * @param Deal $deal
     * @param User $user
     * @return Project|null
     */
    public static function create(Deal $deal, User $user): ?Project
    {
        $project = new Project();
        $project->name = $deal->name;
        $project->description = $deal->description;
        $project->due_date = $deal->due_date;
        $project->customer_id = $deal->customer_id;
        $project->assigned_to = $deal->assigned_to;
        $project->account_id = $deal->account_id;
        $project->user_id = $user->id;
        $project->private_notes = $deal->private_notes;

        return $project;
    }
}