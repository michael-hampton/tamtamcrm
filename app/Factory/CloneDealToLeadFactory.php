<?php


namespace App\Factory;


use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;

class CloneDealToLeadFactory
{
    /**
     * @param Deal $deal
     * @param User $user
     * @return Lead|null
     */
    public static function create(Deal $deal, User $user): ?Lead
    {
        $lead = new Lead();
        $lead->name = $deal->name;
        $lead->description = $deal->description;
        $lead->source_type = $deal->source_type;
        $lead->valued_at = $deal->valued_at ?: 0;
        $lead->due_date = $deal->due_date;
        $lead->assigned_to = $deal->assigned_to;
        $lead->account_id = $deal->account_id;
        $lead->user_id = $user->id;
        $lead->private_notes = $deal->private_notes;
        $lead->public_notes = $deal->public_notes;

        return $lead;
    }
}