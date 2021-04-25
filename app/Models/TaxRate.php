<?php

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class TaxRate extends Model
{

    use SoftDeletes;
    use HasFactory;
    use Archiveable;
    use QueryCacheable;
    use QueryScopes;

    protected static $flushCacheOnUpdate = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'rate'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'tax_rates',
        ];
    }

    /**
     * @param $term
     *
     * @return mixed
     */
    public function searchTaxRate($term)
    {
        return self::search($term);
    }

}
