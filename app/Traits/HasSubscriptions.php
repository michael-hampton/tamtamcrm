<?php


namespace App\Traits;


use App\Components\Subscriptions\Period;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\PlanSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasSubscriptions
{
    /**
     * A model may have many active subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function activeSubscriptions(): Collection
    {
        return $this->subscriptions->reject->inactive();
    }

    /**
     * Get a subscription by slug.
     *
     * @param string $subscriptionSlug
     * @return PlanSubscription|null
     */
    public function subscription(string $subscriptionSlug): ?PlanSubscription
    {
        return $this->subscriptions()->where('slug', $subscriptionSlug)->first();
    }

    /**
     * The subscriber may have many subscriptions.
     *
     * @return MorphMany
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(PlanSubscription::class, 'subscriber', 'subscriber_type', 'subscriber_id');
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param string $related
     * @param string $name
     * @param string $type
     * @param string $id
     * @param string $localKey
     *
     * @return MorphMany
     */
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Get subscribed plans.
     *
     * @return PlanSubscription|null
     */
    public function subscribedPlans(): ?PlanSubscription
    {
        $planIds = $this->subscriptions->reject->inactive()->pluck('plan_id')->unique();

        return app('rinvex.subscriptions.plan')->whereIn('id', $planIds)->get();
    }

    /**
     * Check if the subscriber subscribed to the given plan.
     *
     * @param int $planId
     *
     * @return bool
     */
    public function subscribedTo($planId): bool
    {
        $subscription = $this->subscriptions()->where('plan_id', $planId)->first();

        return $subscription && $subscription->active();
    }

    /**
     * Subscribe subscriber to a new plan.
     *
     * @param $subscription
     * @param Plan $plan
     * @param Account $account
     * @param int $number_of_licences
     * @param Carbon|null $startDate
     * @return PlanSubscription
     */
    public function newSubscription(
        $subscription,
        Plan $plan,
        Account $account,
        int $number_of_licences = 1,
        Carbon $startDate = null
    ): PlanSubscription {
        $trial = new Period($plan->trial_interval, $plan->trial_period, $startDate ?? now());
        $period = new Period($plan->interval_unit, $plan->interval_count, $trial->getEndDate());
        $due_date = new Period($plan->invoice_interval, $plan->invoice_period, $trial->getEndDate());

        return $this->subscriptions()->create(
            [
                'name'               => $subscription,
                'plan_id'            => $plan->getKey(),
                'domain_id'          => $account->domains->id,
                'account_id'         => $account->id,
                'trial_ends_at'      => $trial->getEndDate(),
                'starts_at'          => $period->getStartDate(),
                'ends_at'            => $period->getEndDate(),
                'due_date'           => $due_date->getEndDate(),
                'number_of_licences' => $number_of_licences
            ]
        );
    }
}