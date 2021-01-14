<?php

namespace App\Events\Task;

use App\Models\task;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class TaskWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var task
     */
    public Task $task;

    /**
     * Create a new event instance.
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
        $this->send($task, get_class($this));
    }
}
