<?php

namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseCategory extends Model
{

    use SoftDeletes;
    use Archiveable;
    use HasFactory;

    protected $fillable = [
        'name',
        'column_color'
    ];

    /**
     * @return BelongsTo
     */
    public function cases()
    {
        return $this->belongsTo('App\Models\Cases');
    }
}
