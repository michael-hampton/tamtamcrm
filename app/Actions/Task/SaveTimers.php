<?php

namespace App\Actions\Task;


use App\Factory\TimerFactory;
use App\Models\Task;
use App\Models\Timer;
use App\Repositories\TimerRepository;

class SaveTimers
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @param array $timers
     * @param Task $task
     * @param TimerRepository $timer_repository
     * @return Timer|null
     */
    public function execute(array $timers, Task $task, TimerRepository $timer_repository)
    {
        $task->timers()->forceDelete();

        foreach ($timers as $time) {
            $timer = $timer_repository->save(
                $task,
                TimerFactory::create(
                    auth()->user(),
                    auth()->user()->account_user()->account,
                    $task
                ),
                $time
            );
        }

        return $timer;
    }
}