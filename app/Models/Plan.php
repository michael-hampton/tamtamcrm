<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{

    use SoftDeletes;

    const PLAN_STANDARD = 'STANDARD';
    const PLAN_ADVANCED = 'ADVANCED';
    const PLAN_TRIAL = 'TRIAL';
    const SUBSCRIPTION_FREE = 3;

    const PLAN_PERIOD_YEAR = 'YEARLY';
    const PLAN_PERIOD_MONTH = 'MONTHLY';

    protected $casts = [
        //'subscription_expiry_date' => 'date'
    ];

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'interval_unit',
        'interval_count',
        'trial_period',
        'trial_interval',
        'invoice_period',
        'invoice_interval',
        'grace_period',
        'grace_interval',
        'prorate_day',
        'prorate_period',
        'prorate_extend_due',
        'active_subscribers_limit',
        'sort_order',
        'is_active',
        'currency',
        'signup_fee',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Boot function for using with User Events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Default interval is 1 month
        static::saving(
            function ($model) {
                if (!$model->interval_unit) {
                    $model->interval_unit = 'month';
                }

                if (!$model->interval_count) {
                    $model->interval_count = 1;
                }
            }
        );
    }

    /**
     * The plan may have many features.
     *
     * @return HasMany
     */
    public function features()
    {
        return $this
            ->hasMany(
                PlanFeature::class,
                'plan_id',
                'id'
            );
    }

    /**
     * Get plan subscriptions.
     *
     * @return HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(
            PlanSubscription::class,
            'plan_id',
            'id'
        );
    }

    /**
     * Check if plan is free.
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return ((float)$this->price <= 0.00);
    }

    /**
     * Check if plan has trial.
     *
     * @return bool
     */
    public function hasTrial(): bool
    {
        return $this->trial_period && $this->trial_interval;
    }

    /**
     * Check if plan has grace.
     *
     * @return bool
     */
    public function hasGrace(): bool
    {
        return $this->grace_period && $this->grace_interval;
    }

    /**
     * Activate the plan.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate the plan.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }
}
