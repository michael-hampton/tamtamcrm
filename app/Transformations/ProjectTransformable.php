<?php

namespace App\Transformations;

use App\Models\File;
use App\Models\Project;
use App\Models\Task;

trait ProjectTransformable
{
    use TaskTransformable;

    /**
     * @param Project $project
     * @return array
     */
    protected function transformProject(Project $project)
    {
        return [
            'number'         => $project->number ?: '',
            'id'             => (int)$project->id,
            'customer_name'  => $project->customer->present()->name,
            'name'           => $project->name,
            'description'    => $project->description,
            'is_completed'   => $project->is_completed,
            'due_date'       => $project->due_date,
            'updated_at'     => (int)$project->updated_at,
            'deleted_at'     => $project->deleted_at,
            'created_at'     => $project->created_at,
            'is_deleted'     => (bool)$project->is_deleted,
            'task_rate'      => (float)$project->task_rate,
            'budgeted_hours' => (float)$project->budgeted_hours,
            'account_id'     => $project->account_id,
            'user_id'        => (int)$project->user_id,
            'customer_id'    => (int)$project->customer_id,
            'assigned_to'    => (int)$project->assigned_to,
            'private_notes'  => $project->private_notes ?: '',
            'public_notes'   => $project->public_notes ?: '',
            'custom_value1'  => $project->custom_value1 ?: '',
            'custom_value2'  => $project->custom_value2 ?: '',
            'custom_value3'  => $project->custom_value3 ?: '',
            'custom_value4'  => $project->custom_value4 ?: '',
            'tasks'          => $this->transformProjectTasks($project->tasks),
            'files'          => $this->transformTaskFiles($project->files),
            'column_color'   => $project->column_color ?: '',
        ];
    }

    public function transformProjectTasks($tasks)
    {
        if (empty($tasks)) {
            return [];
        }

        return $tasks->map(
            function (Task $task) {
                return $this->transformTask($task);
            }
        )->all();
    }

    private function transformProjectFiles($files)
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
}
