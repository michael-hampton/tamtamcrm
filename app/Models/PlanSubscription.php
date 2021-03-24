<?php

namespace App\Models;


use App\Components\Subscriptions\Period;
use App\Components\Subscriptions\SubscriptionAbility;
use App\Components\Subscriptions\SubscriptionUsageManager;
use App\Events\Plan\SubscriptionCanceled;
use App\Events\Plan\SubscriptionPlanChanged;
use App\Events\Plan\SubscriptionRenewed;
use App\Models\Concerns\BelongsToPlanModel;
use App\Traits\Archiveable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PlanSubscription extends Model
{
    use BelongsToPlanModel;
    use Archiveable;
    use SoftDeletes;

    /**
     * Subscription statuses
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_ENDED = 'ended';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscriber_id',
        'subscriber_type',
        'plan_id',
        'domain_id',
        'account_id',
        'slug',
        'name',
        'description',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'due_date',
        'cancels_at',
        'canceled_at',
        'number_of_licences',
        'promocode_applied'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'starts_at',
        'ends_at',
        'due_date',
        'canceled_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'promocode_applied' => 'bool'
    ];

    /**
     * @var array
     */
    protected $with = ['plan'];

    /**
     * @var SubscriptionAbility
     */
    protected SubscriptionAbility $ability;

    /**
     * Boot function for using with User Events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(
            function ($model) {
                // Set period if it wasn't set
                if (!$model->starts_at || !$model->ends_at) {
                    $model->setNewPeriod();
                }
            }
        );

        static::saved(
            function ($model) {
                /** @var PlanSubscription $model */
                if ($model->getOriginal('plan_id') && $model->getOriginal('plan_id') !== $model->plan_id) {
                    event(new SubscriptionPlanChanged($model));
                }
            }
        );
    }

    /**
     * Get subscriber.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->morphTo('subscriber', 'subscriber_type', 'subscriber_id', 'id');
    }

    /**
     * Get subscription usage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usage()
    {
        return $this->hasMany(
            PlanSubscriptionUsage::class,
            'subscription_id',
            'id'
        );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'subscriber_id');
    }

    /**
     * Get status attribute.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if ($this->isActive()) {
            return self::STATUS_ACTIVE;
        }

        if ($this->isCanceled()) {
            return self::STATUS_CANCELED;
        }

        if ($this->isEnded()) {
            return self::STATUS_ENDED;
        }
    }

    /**
     * Check if subscription is active.
     *
     * @return bool
     */
    public function active(): bool
    {
        return !$this->ended() || $this->onTrial();
    }

    /**
     * Check if subscription is inactive.
     *
     * @return bool
     */
    public function inactive(): bool
    {
        return !$this->active();
    }

    /**
     * Check if subscription is currently on trial.
     *
     * @return bool
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at ? Carbon::now()->lt($this->trial_ends_at) : false;
    }

    /**
     * Check if subscription is canceled.
     *
     * @return bool
     */
    public function canceled(): bool
    {
        return $this->canceled_at ? Carbon::now()->gte($this->canceled_at) : false;
    }

    /**
     * Check if subscription period has ended.
     *
     * @return bool
     */
    public function ended(): bool
    {
        return $this->ends_at ? Carbon::now()->gte($this->ends_at) : false;
    }

    /**
     * Check if subscription is canceled immediately.
     *
     * @return bool
     */
    public function isCanceledImmediately(): bool
    {
        return !is_null($this->canceled_at) && $this->canceled_immediately === true;
    }

    /**
     * Check if subscription period has ended.
     *
     * @return bool
     */
    public function isEnded(): bool
    {
        $endsAt = Carbon::instance($this->ends_at);

        return Carbon::now()->gte($endsAt);
    }

    /**
     * Cancel subscription.
     *
     * @param bool $immediately
     * @return PlanSubscription
     * @throws \Throwable
     */
    public function cancel($immediately = false)
    {
        $this->canceled_at = Carbon::now();

        if ($immediately) {
            $this->canceled_immediately = true;
            $this->ends_at = $this->canceled_at;
        }

        $this->saveOrFail();

        event(new SubscriptionCanceled($this));

        return $this;
    }

    /**
     * @param $plan
     * @return $this
     */
    public function changePlan($plan)
    {
        // If plans does not have the same billing frequency
        // (e.g., invoice_interval and invoice_period) we will update
        // the billing dates starting today, and sice we are basically creating
        // a new billing cycle, the usage data will be cleared.
        if ($this->plan->invoice_interval !== $plan->invoice_interval || $this->plan->invoice_period !== $plan->invoice_period) {
            // Set period
            $this->setNewPeriod(
                $plan->interval_unit,
                $plan->interval_count,
                $plan->invoice_interval,
                $plan->invoice_period
            );

            // Clear usage data
            $usageManager = new SubscriptionUsageManager($this);
            $usageManager->clear();
        }

        // Attach new plan to subscription
        $this->plan_id = $plan->id;
        $this->save();

        return $this;
    }

    /**
     * Renew subscription period.
     *
     * @return self
     * @throws LogicException
     */
    public function renew()
    {
        if ($this->ended() && $this->canceled()) {
            throw new \LogicException('Unable to renew canceled ended subscription.');
        }

        $subscription = $this;

        DB::transaction(
            function () use ($subscription) {
                // Clear usage data
                $usageManager = new SubscriptionUsageManager($subscription);
                $usageManager->clear();

                // Renew period
                $subscription->setNewPeriod();
                $subscription->cancelled_at = null;
                $subscription->save();
            }
        );

        event(new SubscriptionRenewed($this));

        return $this;
    }

    /**
     * @return SubscriptionAbility
     */
    public function ability()
    {
        if (is_null($this->ability)) {
            return new SubscriptionAbility($this);
        }

        return $this->ability;
    }

    /**
     * @param $query
     * @param $subscriber
     * @return mixed
     */
    public function scopeBySubscriber($query, $subscriber)
    {
        return $query->where('subscriber_id', $subscriber->getKey())
                     ->where('subscriber_type', get_class($subscriber));
    }

    /**
     * Find subscription with an ending trial.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dayRange
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindEndingTrial($query, $dayRange = 3)
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDays($dayRange);

        return $query->whereBetween('trial_ends_at', [$from, $to]);
    }

    /**
     * Find subscription with an ended trial.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindEndedTrial($query)
    {
        return $query->where('trial_ends_at', '<=', Carbon::now());
    }

    /**
     * Find ending subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dayRange
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindEndingPeriod($query, $dayRange = 3)
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDays($dayRange);

        return $query->whereBetween('ends_at', [$from, $to]);
    }

    /**
     * Find ended subscriptions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindEndedPeriod($query)
    {
        return $query->where('ends_at', '<=', Carbon::now());
    }

    /**
     * Set subscription period.
     *
     * @param string $intervalUnit
     * @param int $intervalCount
     * @param null|int|string|\DateTime $startAt Start time
     * @return  PlanSubscription
     */
    protected function setNewPeriod(
        string $intervalUnit = '',
        int $intervalCount = 0,
        string $invoicePeriod = '',
        int $invoiceCount = 0,
        $startAt = null
    ) {
        if (empty($intervalUnit)) {
            $intervalUnit = $this->plan->interval_unit;
        }

        if (empty($intervalCount)) {
            $intervalCount = $this->plan->interval_count;
        }

        if (empty($invoicePeriod)) {
            $invoicePeriod = $this->plan->invoice_interval;
        }

        if (empty($invoiceCount)) {
            $invoiceCount = $this->plan->invoice_period;
        }

        $period = new Period($intervalUnit, $intervalCount, $startAt);

        $this->starts_at = $period->getStartDate();
        $this->ends_at = $period->getEndDate();

        $period = new Period($invoicePeriod, $invoiceCount, $period->getStartDate());

        $this->due_date = $period->getEndDate();

        return $this;
    }

    public function domain()
    {
        return $this->belongsTo(
            Domain::class,
            'domain_id',
            'id'
        );
    }
}