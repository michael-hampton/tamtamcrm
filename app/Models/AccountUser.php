<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountUser extends Pivot
{
    use SoftDeletes;

    //   protected $guarded = ['id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'updated_at'    => 'timestamp',
        'created_at'    => 'timestamp',
        'deleted_at'    => 'timestamp',
        'settings'      => 'object',
        'notifications' => 'object',
        'permissions'   => 'string',
    ];
    protected $fillable = [
        'notifications',
        'account_id',
        'permissions',
        'settings',
        'is_admin',
        'is_owner',
        'is_locked',
        'slack_webhook_url',
    ];

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
