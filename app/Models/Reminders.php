<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Reminders extends Model
{

    public $fillable = [
        'account_id',
        'user_id',
        'amount_to_charge',
        'amount_type',
        'scheduled_to_send',
        'number_of_days_after',
        'enabled',
        'subject',
        'message'
    ];

    public $casts = [
        'enabled' => 'bool'
    ];
}