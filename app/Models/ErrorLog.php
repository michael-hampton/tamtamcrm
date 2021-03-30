<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ErrorLog extends Authenticatable
{
    use Notifiable, SoftDeletes, QueryCacheable;

    /**
     * type
     */
    const PAYMENT = 'payment';
    const REFUND = 'refund';
    const EMAIL = 'email';
    /**
     * result
     */
    const SUCCESS = 'success';
    const NEUTRAL = 'neutral';
    const FAILURE = 'failure';
    protected static $flushCacheOnUpdate = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data',
        'error_type',
        'error_result',
        'entity',
        'entity_id',
        'account_id',
        'user_id',
        'customer_id',

    ];
    protected $casts = [
        'data' => 'object'
    ];
    protected $table = 'error_log';

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'error_log',
        ];
    }
}
