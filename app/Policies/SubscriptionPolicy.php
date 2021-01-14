<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return mixed
     */
    public function view(User $user, Subscription $subscription)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $subscription->user_id === $user->id || $user->hasPermissionTo(
                'subscriptioncontroller.show'
            ) || (!empty($subscription->assigned_to) && $subscription->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return mixed
     */
    public function delete(User $user, Subscription $subscription)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $subscription->user_id === $user->id || $user->hasPermissionTo(
                'subscriptioncontroller.destroy'
            ) || (!empty($subscription->assigned_to) && $subscription->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return mixed
     */
    public function update(User $user, Subscription $subscription)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $subscription->user_id === $user->id || $user->hasPermissionTo(
                'subscriptioncontroller.update'
            ) || (!empty($subscription->assigned_to) && $subscription->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $user->hasPermissionTo(
                'subscriptioncontroller.store'
            );
    }
}
