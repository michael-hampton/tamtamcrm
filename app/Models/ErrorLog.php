<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ErrorLog extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * type
     */
    const PAYMENT = 'payment';
    const EMAIL = 'email';
    /**
     * result
     */
    const SUCCESS = 'success';
    const NEUTRAL = 'neutral';
    const FAILURE = 'failure';
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
}
