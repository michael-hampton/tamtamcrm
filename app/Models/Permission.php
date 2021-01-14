<?php

namespace App\Models;

use App\Models;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    use SearchableTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description'
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
            'permissions.name' => 10
        ]
    ];

    /**
     * @param $term
     *
     * @return mixed
     */
    public function searchPermission($term)
    {
        return self::search($term);
    }

    public function roles()
    {
        return $this->belongsToMany(Models\Role::class, 'permission_role');
    }

}
