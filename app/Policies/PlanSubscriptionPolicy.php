<?php

namespace App\Policies;

use App\Models\PlanSubscription;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanSubscriptionPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param PlanSubscription $plan_subscription
     * @return bool
     */
    public function view(User $user, PlanSubscription $plan_subscription)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param TaskStatus $taskStatus
     * @return mixed
     */
    public function delete(User $user, PlanSubscription $plan_subscription)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param TaskStatus $taskStatus
     * @return mixed
     */
    public function update(User $user, PlanSubscription $plan_subscription)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner;
    }
}
