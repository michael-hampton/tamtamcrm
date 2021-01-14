<?php

namespace App\Models;

use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Department extends Model
{

    use SearchableTrait, NodeTrait, HasFactory;

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'departments.name' => 10
        ]
    ];
    protected $fillable = ['name', 'department_manager', 'parent_id'];

    /**
     * @param $term
     *
     * @return mixed
     */
    public function searchDepartment($term)
    {
        return self::search($term);
    }

    public function departmentManager()
    {
        return $this->belongsTo(User::class);
    }

}
