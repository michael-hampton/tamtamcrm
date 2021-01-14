<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Design extends Model
{

    use SoftDeletes, HasFactory;

    protected $casts = [
        'design' => 'object'
    ];

    protected $fillable = [
        'name',
        'design',
        'is_active',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

}
