<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $casts = [
        'data' => 'object'
    ];

    protected $fillable = [
        'data',
        'entity_class',
        'entity_id',
        'notification_id'
    ];
}