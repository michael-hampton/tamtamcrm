<?php

namespace App\Models;

use App\Traits\Archiveable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static byCode(string $code)
 * @method static pluck(string $string)
 * @method static insert(array $records)
 */
class Promocode extends Model
{
    use SoftDeletes;
    use Archiveable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
        'amount_type',
        'reward',
        'is_disposable',
        'expires_at',
        'quantity'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_disposable' => 'boolean',
        'data'          => 'array',
        'quantity'      => 'integer'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expires_at'];

    /**
     * Promocode constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('promocodes.table', 'promocodes');
    }

    /**
     * Get the users who is related promocode.
     *
     * @return BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(
            config('promocodes.customer_model'),
            config('promocodes.relation_table'),
            config('promocodes.foreign_pivot_key', 'customer_id'),
            config('promocodes.related_pivot_key', 'customer_id')
        )
                    ->withPivot('used_at');
    }

    /**
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(PromocodeUser::class);
    }

    /**
     * Query builder to find promocode using code.
     *
     * @param $query
     * @param $code
     *
     * @return mixed
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Query builder to get disposable codes.
     *
     * @param $query
     * @return mixed
     */
    public function scopeIsDisposable($query)
    {
        return $query->where('is_disposable', true);
    }

    /**
     * Query builder to get non-disposable codes.
     *
     * @param $query
     * @return mixed
     */
    public function scopeIsNotDisposable($query)
    {
        return $query->where('is_disposable', false);
    }

    /**
     * Query builder to get expired promotion codes.
     *
     * @param $query
     * @return mixed
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')->whereDate('expires_at', '<=', Carbon::now());
    }

    /**
     * Check if code is disposable (ont-time).
     *
     * @return bool
     */
    public function isDisposable()
    {
        return $this->is_disposable;
    }

    /**
     * Check if code is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
    }

    /**
     * Check if code amount is over.
     *
     * @return bool
     */
    public function isOverAmount()
    {
        if (is_null($this->quantity)) {
            return false;
        }

        return $this->quantity <= 0;
    }
}
