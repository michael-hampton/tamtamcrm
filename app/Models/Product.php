<?php

namespace App\Models;

use App\Services\Product\ProductService;
use App\Traits\Archiveable;
use App\Traits\ManageStock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;
    use ManageStock;
    use HasFactory;
    use Archiveable;

    public const MASS_UNIT = [
        'OUNCES' => 'oz',
        'GRAMS'  => 'gms',
        'POUNDS' => 'lbs'
    ];

    public const DISTANCE_UNIT = [
        'CENTIMETER' => 'cm',
        'METER'      => 'mtr',
        'INCH'       => 'in',
        'MILIMETER'  => 'mm',
        'FOOT'       => 'ft',
        'YARD'       => 'yd'
    ];

    protected $casts = [
        'is_featured' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'quantity',
        'reserved_stock',
        'cover',
        'description',
        'price',
        'cost',
        'status',
        'company_id',
        'sale_price',
        'length',
        'width',
        'height',
        'distance_unit',
        'weight',
        'mass_unit',
        'slug',
        'is_featured',
        'account_id',
        'assigned_to',
        'user_id',
        'notes',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'brand_id'
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return HasMany
     */
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    /**
     * @return HasMany
     */
    public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }
}
