<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class AccountUser extends Pivot
{
    use SoftDeletes;
    use QueryCacheable;

    //   protected $guarded = ['id'];
    protected static $flushCacheOnUpdate = true;
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'updated_at'    => 'timestamp',
        'created_at'    => 'timestamp',
        'deleted_at'    => 'timestamp',
        'notifications' => 'object',
    ];
    protected $fillable = [
        'notifications',
        'account_id',
        'is_admin',
        'is_owner',
        'is_locked',
        'slack_webhook_url',
    ];

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'account_user',
        ];
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
