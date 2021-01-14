<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{

    protected $table = 'task_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function taskStatus()
    {
        return $this->hasMany(TaskStatus::class);
    }

}
