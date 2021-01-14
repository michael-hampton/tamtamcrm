<?php

namespace App\Events\Task;

use App\Models\task;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class TaskWasEmailed
{
    use SerializesModels;

    /**
     * @var task
     */
    public Task $task;

    /**
     * Create a new event instance.
     *
     * @param Task $task
     */
    public function __construct(task $task)
    {
        $this->task = $task;
    }
}
