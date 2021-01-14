<?php


namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductFeature
 * @package App
 */
class ProductFeature extends Model
{
    use Archiveable;

    protected $fillable = [
        'description',
        'product_id'
    ];
}
