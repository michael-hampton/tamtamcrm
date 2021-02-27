<?php

namespace App\Jobs\Task;

use App\Models\Task;
use App\Models\Timer;
use App\Models\User;
use App\Repositories\TaskRepository;
use App\Traits\CalculateRecurring;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class TaskOverlap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CalculateRecurring;

    /**
     * @var Task
     */
    private Task $task;

    /**
     * @var TaskRepository
     */
    private TaskRepository $task_repo;

    private User $user;

    /**
     * SendRecurringTask constructor.
     * @param TaskRepository $task_repo
     */
    public function __construct(TaskRepository $task_repo, User $user)
    {
        $this->task_repo = $task_repo;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->findOverlappingTasks();
    }

    private function findOverlappingTasks()
    {
        $user = $this->user;

        $query = Timer::whereExists(
            function ($query) use ($user) {
                $query->select('*')
                      ->from('timers AS b')
                      ->whereRaw(
                          'timers.id != b.id 
                          AND timers.task_id = b.task_id
                          AND b.user_id = timers.user_id
    and ( timers.started_at between b.started_at and DATE_ADD(b.stopped_at, INTERVAL 5 MINUTE) 
    or timers.stopped_at between b.started_at and DATE_ADD(b.stopped_at, INTERVAL 5 MINUTE) 
    or b.started_at between timers.started_at and DATE_ADD(timers.stopped_at, INTERVAL 5 MINUTE) ) 
    and timers.started_at != b.stopped_at 
    and b.started_at != timers.stopped_at'
                      );
            }
        )->where('timers.user_id', '=', $user->id);

        return $query->count();
    }
}
