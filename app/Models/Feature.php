<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{

    protected $fillable = [
        'name',
        'code',
        'description',
        'interval_unit',
        'interval_count',
        'sort_order'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_features', 'feature_id', 'plan_id')->using('plan_feature');
    }

    public function usage()
    {
        return $this->hasMany(PlanSubscriptionUsage::class, 'feature_code', 'code');
    }
}