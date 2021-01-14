<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProductListingHistory extends Model
{
    protected $table = 'product_listing_history';

    protected $casts = [
        'changes' => 'object'
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'product_id',
        'changes'
    ];
}