<?php

namespace App\Http\Controllers;

use App\Models\Timer;
use App\Repositories\TaskRepository;
use App\Repositories\TimerRepository;
use App\Requests\Timer\CreateTimerRequest;
use Carbon\Carbon;

class TimerController extends Controller
{

    private $task_repo;

    private $timer_repo;

    public function __construct(TaskRepository $task_repo, TimerRepository $timer_repo)
    {
        $this->timer_repo = $timer_repo;
        $this->task_repo = $task_repo;
    }

    public function store(CreateTimerRequest $request)
    {
        $task = $this->task_repo->findTaskById($request->task_id);

        $timer = $task->service()->saveTimers($request->input('time_log'), $task, $this->timer_repo);

        return response()->json($timer);
    }

    public function running()
    {
        return Timer::with('task')->mine()->running()->first() ?? [];
    }

    public function stopRunning()
    {
        if ($timer = Timer::mine()->running()->first()) {
            $timer->update(['stopped_at' => new Carbon]);
        }

        return $timer;
    }

}
