<?php

namespace App\Models\Concerns;


use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPlanModel
{
    /**
     * Get plan.
     *
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(
            Plan::class,
            'plan_id',
            'id'
        );
    }

    /**
     * Scope by plan id.
     *
     * @param Builder
     * @param int $planId
     * @return Builder
     */
    public function scopeByPlan($query, $planId)
    {
        return $query->where('plan_id', $planId);
    }
}