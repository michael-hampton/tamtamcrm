<?php

namespace App\Events\Task;

use App\Models\task;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class TaskWasUpdated
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
    public function __construct(task $task)
    {
        $this->task = $task;
        $this->send($task, get_class($this));
    }
}
