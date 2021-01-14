<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timer extends Model
{
    use SoftDeletes;

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'user_id',
        'task_id',
        'stopped_at',
        'started_at',
        'name'
    ];

    /**
     * {@inheritDoc}
     */
    protected $dates = ['started_at', 'stopped_at'];

    /**
     * {@inheritDoc}
     */
    protected $with = ['user'];

    /**
     * Get the related user.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related project
     *
     * @return BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get timer for current user.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeMine($query)
    {
        return $query->whereUserId(auth()->user()->id);
    }

    /**
     * Get the running timers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRunning($query)
    {
        return $query->whereNull('stopped_at');
    }
}
