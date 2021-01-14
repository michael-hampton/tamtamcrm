<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\Timer;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class TimerRepository extends BaseRepository
{
    /**
     * TimerRepository constructor.
     * @param Timer $timer
     */
    public function __construct(Timer $timer)
    {
        parent::__construct($timer);
        $this->model = $timer;
    }

    /**
     * Gets the class name.
     *
     * @return     string The class name.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return Timer
     */
    public function findTimerById(int $id): Timer
    {
        return $this->findOneOrFail($id);
    }

    public function getTotalDuration(Task $task)
    {
        $timers = DB::table('timers')
                    ->select(\DB::raw('ROUND(TIMESTAMPDIFF(MINUTE, started_at, stopped_at)/60, 2) as hours'))
                    ->where('task_id', '=', $task->id)
                    ->get();


        $total = array_sum(array_column($timers->toArray(), 'hours'));

        return number_format((float)$total, 2, '.', '');
    }

    public function isRunning(Task $task)
    {
        $timer = Timer::whereNull('stopped_at')->where('task_id', '=', $task->id)->first();

        return !empty($timer);
    }

    public function getStartTime(Task $task)
    {
        $timer = Timer::where('task_id', '=', $task->id)->first();

        if (empty($timer)) {
            return null;
        }

        return $timer->started_at;
    }

    public function getEndTime(Task $task)
    {
        $timer = Timer::where('task_id', '=', $task->id)->last();

        if (empty($timer)) {
            return null;
        }

        return $timer[0]['stopped_at'];
    }

    public function stopTimer(Task $task): ?Timer
    {
        $timer = $task->timers()->orderBy('started_at', 'desc')->first();

        if (!empty($timer)) {
            $timer->stopped_at = date('Y-m-d H:i:s');
            $timer->save();
        }

        return $timer;
    }

    public function startTimer(Timer $timer, Task $task): ?Timer
    {
        $timer = $this->save(
            $task,
            $timer,
            [
                'date'       => date('Y-m-d'),
                'start_time' => date('H:i:s'),
                'end_time'   => '',
                'name'       => date('Y-m-d H:i:s')
            ]
        );

        return $timer;
    }

    /**
     * @param Task $task
     * @param Timer $timer
     * @param array $data
     * @return Timer|null
     */
    public function save(Task $task, Timer $timer, array $data): ?Timer
    {
        $start_time = date('H:i:s', strtotime($data['start_time']));
        $end_time = date('H:i:s', strtotime($data['end_time']));

        $end_date = !empty($data['end_date']) ? $data['end_date'] : date('Y-m-d');

        $timer->started_at = date('Y-m-d H:i:s', strtotime($data['date'] . ' ' . $start_time));
        $timer->stopped_at = empty($data['end_time']) ? null : $end_date . $end_time;
        $timer->task_id = $task->id;

        $timer->save();

        return $timer;
    }
}
