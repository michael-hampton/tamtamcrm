<?php

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseTemplate extends Model
{
    use SoftDeletes;
    use Archiveable;
    use QueryScopes;

    protected $fillable = [
        'name',
        'send_on',
        'description'
    ];
}
