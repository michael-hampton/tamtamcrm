<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceType extends Model
{
    protected $table = 'source_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
