<?php

namespace App\Models;

use App\Models;
use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getRolePermissions(User $user)
    {
        return DB::table('permission_role AS pr')->select(
            'pr.role_id',
            'p.*',
            DB::raw('IF(ru.user_id, 1, 0) AS has_permission')
        )->join('permissions AS p', 'p.id', '=', 'pr.permission_id')
         ->leftJoin('role_user AS ru', 'ru.role_id', '=', 'pr.role_id')
         ->where('ru.user_id', '=', $user->id)
         ->get();
    }

}
