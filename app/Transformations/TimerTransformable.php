<?php

namespace App\Transformations;


use App\Models\Timer;

class TimerTransformable
{

    /**
     * @param Timer $timer
     * @return array
     */
    public function transform(Timer $timer)
    {
        return [
            'id'         => (int)$timer->id,
            'account_id' => (int)$timer->account_id,
            'user_id'    => (int)$timer->user_id,
            'updated_at' => $timer->updated_at,
            'created_at' => $timer->created_at,
            //'is_deleted' => (bool)$subscription->is_deleted,
            'start_time' => !empty($timer->started_at) ? date('H:i:s', strtotime($timer->started_at)) : '',
            'date'       => date('Y-m-d', strtotime($timer->started_at)),
            'end_time'   => !empty($timer->stopped_at) ? date('H:i:s', strtotime($timer->stopped_at)) : '',
            'end_date'   => !empty($timer->stopped_at) ? date('Y-m-d', strtotime($timer->stopped_at)) : '',
            'task_id'    => (int)$timer->task_id,
        ];
    }
}
