<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'send_on',
        'description'
    ];
}
