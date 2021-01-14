<?php

namespace App\Transformations;

use App\Models\Email;
use App\Models\Lead;

trait LeadTransformable
{
    /**
     * @param Lead $lead
     * @return array
     */
    protected function transformLead(Lead $lead)
    {
        return [
            'id'             => (int)$lead->id,
            'number'         => $lead->number ?: '',
            'design_id'      => (int)$lead->design_id,
            'industry_id'    => (int)$lead->industry_id,
            'created_at'     => $lead->created_at,
            'first_name'     => $lead->first_name,
            'last_name'      => $lead->last_name,
            'name'           => $lead->name,
            'description'    => $lead->description,
            'valued_at'      => $lead->valued_at,
            'source_type'    => $lead->source_type,
            'task_status_id' => $lead->task_status_id,
            'status_name'    => !empty($lead->taskStatus) ? $lead->taskStatus->name : '',
            'task_status'    => $lead->taskStatus,
            'address_1'      => $lead->address_1,
            'address_2'      => $lead->address_2,
            'city'           => $lead->city,
            'zip'            => $lead->zip,
            'email'          => $lead->email,
            'phone'          => $lead->phone,
            'company_name'   => $lead->company_name,
            'job_title'      => $lead->job_title,
            'assigned_to'    => (int)$lead->assigned_to,
            'project_id'     => (int)$lead->project_id,
            'project'        => $lead->project,
            'deleted_at'     => $lead->deleted_at,
            'website'        => $lead->website ?: '',
            'private_notes'  => $lead->private_notes ?: '',
            'public_notes'   => $lead->public_notes ?: '',
            'emails'         => $this->transformLeadEmails($lead->emails()),
            'is_deleted'     => (bool)$lead->is_deleted,
            'column_color'   => $lead->column_color ?: '',
        ];
    }

    /**
     * @param $emails
     * @return array
     */
    private function transformLeadEmails($emails)
    {
        if ($emails->count() === 0) {
            return [];
        }

        return $emails->map(
            function (Email $email) {
                return (new EmailTransformable())->transformEmail($email);
            }
        )->all();
    }
}
