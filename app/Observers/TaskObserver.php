<?php


namespace App\Observers;


use App\Models\Task;
use App\Models\TaskStatus;

class TaskObserver
{
    /**
     * @param Task $task
     */
    public function creating(Task $task)
    {
        if (empty($task->task_status_id)) {
            $task_status = TaskStatus::ByTaskType(1)->orderBy('order_id', 'asc')->first();
            $task->task_status_id = $task_status->id;
        }

        if (is_null($task->order_id)) {
            $task->order_id = Task::max('order_id') + 1;
            return;
        }

        $lowerPriorityTasks = Task::where('order_id', '>=', $task->order_id)->get();

        foreach ($lowerPriorityTasks as $lowerPriorityTask) {
            $lowerPriorityTask->order_id++;
            $lowerPriorityTask->saveQuietly();
        }
    }

    /**
     * @param Task $task
     */
    public function updating(Task $task)
    {
        if ($task->isClean('order_id')) {
            return;
        }

        if (is_null($task->order_id)) {
            $task->order_id = Task::max('order_id');
        }

        if ($task->getOriginal('order_id') > $task->order_id) {
            $positionRange = [
                $task->order_id,
                $task->getOriginal('order_id')
            ];
        } else {
            $positionRange = [
                $task->getOriginal('order_id'),
                $task->order_id
            ];
        }

        $lowerPriorityTasks = Task::where('id', '!=', $task->id)
                                  ->whereBetween('order_id', $positionRange)
                                  ->get();

        foreach ($lowerPriorityTasks as $lowerPriorityTask) {
            if ($task->getOriginal('order_id') < $task->order_id) {
                $lowerPriorityTask->order_id--;
            } else {
                $lowerPriorityTask->order_id++;
            }
            $lowerPriorityTask->saveQuietly();
        }
    }

    /**
     * @param Task $task
     */
    public function deleted(Task $task)
    {
        $lowerPriorityTasks = Task::where('order_id', '>', $task->order_id)->get();

        foreach ($lowerPriorityTasks as $lowerPriorityTask) {
            $lowerPriorityTask->order_id--;
            $lowerPriorityTask->saveQuietly();
        }
    }
}