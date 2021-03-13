<?php

namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseTemplate extends Model
{
    use SoftDeletes;
    use Archiveable;

    protected $fillable = [
        'name',
        'send_on',
        'description'
    ];
}
