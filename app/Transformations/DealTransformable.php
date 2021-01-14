<?php

namespace App\Transformations;

use App\Libraries\Utils;
use App\Models\Deal;
use App\Models\Email;
use App\Models\File;

trait DealTransformable
{

    /**
     * @param Deal $deal
     * @return array
     */
    protected function transformDeal(Deal $deal)
    {
        return [
            'id'             => (int)$deal->id,
            'number'         => $deal->number,
            'design_id'      => (int)$deal->design_id,
            'name'           => $deal->name,
            'description'    => $deal->description,
            'comments'       => $deal->comments,
            'due_date'       => $deal->due_date,
            'task_status_id' => (int)$deal->task_status_id,
            'assigned_to'    => (int)$deal->assigned_to,
            'project_id'     => (int)$deal->project_id,
            'project'        => $deal->project,
            'deleted_at'     => $deal->deleted_at,
            'rating'         => $deal->rating,
            'customer_id'    => $deal->customer_id,
            'valued_at'      => $deal->valued_at,
            'source_type'    => $deal->source_type,
            'is_deleted'     => (bool)$deal->is_deleted,
            'column_color'   => $deal->column_color ?: '',
            'custom_value1'  => $deal->custom_value1 ?: '',
            'custom_value2'  => $deal->custom_value2 ?: '',
            'custom_value3'  => $deal->custom_value3 ?: '',
            'custom_value4'  => $deal->custom_value4 ?: '',
            'public_notes'   => $deal->public_notes ?: '',
            'private_notes'  => $deal->private_notes ?: '',
            'files'          => $this->transformDealFiles($deal->files),
            'emails'         => $this->transformDealEmails($deal->emails()),
            'status_name'    => !empty($deal->taskStatus) ? $deal->taskStatus->name : '',
            'task_status'    => $deal->taskStatus
        ];
    }

    private function transformDealFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        return $files->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }

    private function transformDealEmails($emails)
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
