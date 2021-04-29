<?php


namespace App\Observers;


use App\Models\Lead;
use App\Models\TaskStatus;

class LeadObserver
{
    /**
     * @param Lead $lead
     */
    public function creating(Lead $lead)
    {
        if (empty($lead->task_status_id)) {
            $task_status = TaskStatus::ByTaskType(3)->orderBy('order_id', 'asc')->first();
            $lead->task_status_id = $task_status->id;
        }

        if (is_null($lead->order_id)) {
            $lead->order_id = Lead::max('order_id') + 1;
            return;
        }

        $lowerPriorityLeads = Lead::where('order_id', '>=', $lead->order_id)->get();

        foreach ($lowerPriorityLeads as $lowerPriorityLead) {
            $lowerPriorityLead->order_id++;
            $lowerPriorityLead->saveQuietly();
        }
    }

    /**
     * @param Lead $lead
     */
    public function updating(Lead $lead)
    {
        if ($lead->isClean('order_id')) {
            return;
        }

        if (is_null($lead->order_id)) {
            $lead->order_id = Lead::max('order_id');
        }

        if ($lead->getOriginal('order_id') > $lead->order_id) {
            $positionRange = [
                $lead->order_id,
                $lead->getOriginal('order_id')
            ];
        } else {
            $positionRange = [
                $lead->getOriginal('order_id'),
                $lead->order_id
            ];
        }

        $lowerPriorityLeads = Lead::where('id', '!=', $lead->id)
                                  ->whereBetween('order_id', $positionRange)
                                  ->get();

        foreach ($lowerPriorityLeads as $lowerPriorityLead) {
            if ($lead->getOriginal('order_id') < $lead->order_id) {
                $lowerPriorityLead->order_id--;
            } else {
                $lowerPriorityLead->order_id++;
            }
            $lowerPriorityLead->saveQuietly();
        }
    }

    /**
     * @param Lead $lead
     */
    public function deleted(Lead $lead)
    {
        $lowerPriorityLeads = Lead::where('order_id', '>', $lead->order_id)->get();

        foreach ($lowerPriorityLeads as $lowerPriorityLead) {
            $lowerPriorityLead->order_id--;
            $lowerPriorityLead->saveQuietly();
        }
    }
}