<?php

namespace App\Models;

use App\Traits\SearchableTrait;
use App\type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{

    use SearchableTrait;
    use HasFactory;

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'roles.name' => 10
        ]
    ];
    protected $fillable = ['name', 'description'];

    /**
     *
     * @param mixed ...$permissions
     * @return void
     */
    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        foreach ($permissions as $permission) {
            $this->permissions()->sync($permission, false);
        }
    }

    /**
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function attachPermission(Permission $permission)
    {
        $this->permissions()->attach([$permission->id]);
    }

    /**
     * @param $term
     *
     * @return mixed
     */
    public function searchRole($term)
    {
        return self::search($term);
    }

}
