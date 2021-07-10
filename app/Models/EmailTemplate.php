<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{

    public $fillable = [
        'account_id',
        'user_id',
        'template',
        'subject',
        'message',
        'amount_to_charge',
        'frequency_id',
        'percent_to_charge',
        'enabled'
    ];
}