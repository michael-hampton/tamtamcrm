<?php

namespace App\Transformations;

use App\Libraries\Utils;
use App\Models\Email;
use App\Models\File;
use App\Models\Task;
use App\Models\Timer;
use App\Repositories\TimerRepository;
use Exception;

trait TaskTransformable
{
    use CustomerTransformable;

    /**
     * @param Task $task
     * @return array
     * @throws Exception
     */
    protected function transformTask(Task $task)
    {
        return [
            'id'                   => (int)$task->id,
            'number'               => (string)$task->number,
            'customer_name'        => $task->customer->present()->name,
            'name'                 => $task->name,
            'description'          => $task->description,
            'design_id'            => (int)$task->design_id,
            'comments'             => $task->comments,
            'due_date'             => $task->due_date,
            'start_date'           => $task->start_date ?: '',
            'is_completed'         => $task->is_completed,
            'task_status'          => $task->taskStatus,
            'task_status_id'       => (int)$task->task_status_id,
            'deleted_at'           => $task->deleted_at,
            'customer'             => $this->transformCustomer($task->customer),
            'customer_id'          => $task->customer_id,
            'assigned_to'          => $task->assigned_to,
            'users'                => $task->users,
            'contributors'         => $task->users()->pluck('user_id')->all(),
            'is_active'            => $task->is_active,
            'project_id'           => $task->project_id,
            'is_deleted'           => (bool)$task->is_deleted,
            'timers'               => $this->transformTimers($task->timers),
            'custom_value1'        => $task->custom_value1 ?: '',
            'custom_value2'        => $task->custom_value2 ?: '',
            'custom_value3'        => $task->custom_value3 ?: '',
            'custom_value4'        => $task->custom_value4 ?: '',
            'public_notes'         => $task->public_notes ?: '',
            'private_notes'        => $task->private_notes ?: '',
            'duration'             => (new TimerRepository(new Timer()))->getTotalDuration($task),
            'calculated_task_rate' => $task->getTaskRate(),
            'task_rate'            => $task->task_rate,
            'task_sort_order'      => (int)$task->task_status_sort_order,
            'files'                => $this->transformTaskFiles($task->files),
            'emails'               => $this->transformTaskEmails($task->emails()),
            'is_recurring'         => (bool)$task->is_recurring ?: false,
            'recurring_start_date' => $task->recurring_start_date ?: '',
            'recurring_end_date'   => $task->recurring_end_date ?: '',
            'recurring_due_date'   => $task->recurring_due_date ?: '',
            'last_sent_date'       => $task->last_sent_date ?: '',
            'next_send_date'       => $task->next_send_date ?: '',
            'recurring_frequency'  => (string)$task->recurring_frequency ?: '',
            'project'              => $task->project,
            'invoice_id'           => $task->invoice_id,
            'invoice'              => $task->invoice,
            'include_documents'    => (bool)$task->include_documents,
            'status_name'          => !empty($task->taskStatus) ? $task->taskStatus->name : '',
            'column_color'         => $task->column_color ?: '',
        ];
    }

    /**
     * @param $timers
     * @return array
     */
    private function transformTimers($timers)
    {
        if ($timers->count() === 0) {
            return [];
        }

        return $timers->map(
            function (Timer $timer) {
                return (new TimerTransformable)->transform($timer);
            }
        )->all();
    }

    private function transformTaskFiles($files)
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

    private function transformTaskEmails($emails)
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
