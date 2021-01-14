<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Email extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'body',
        'design',
        'template',
        'recipient',
        'recipient_email',
        'sent_at',
        'entity',
        'entity_id',
        'account_id',
        'user_id',
        'direction',
        'failed_to_send'
    ];

    protected $casts = [
        'design' => 'object'
    ];
}
