<?php

namespace App\Models;

use App\Traits\Archiveable;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{

    use SearchableTrait;
    use HasFactory;
    use Archiveable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'column_color',
        'task_type'
    ];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'task_statuses.name' => 10
        ]
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type');
    }

    /**
     * @param $term
     *
     * @return mixed
     */
    public function searchTaskStatus($term)
    {
        return self::search($term);
    }

}
