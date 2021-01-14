<?php

namespace App\Transformations;

use App\Models\TaskStatus;

trait TaskStatusTransformable
{

    /**
     * @param TaskStatus $taskStatus
     * @return array
     */
    public function transformTaskStatus(TaskStatus $taskStatus)
    {
        return [
            'id'           => (int)$taskStatus->id,
            'name'         => $taskStatus->name,
            'description'  => $taskStatus->description,
            'task_count'   => $taskStatus->tasks->count(),
            'column_color' => $taskStatus->column_color
        ];
    }

}
