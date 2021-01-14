<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Quote $quote
     * @return mixed
     */
    public function view(User $user, Quote $quote)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $quote->user_id === $user->id || $user->hasPermissionTo(
                'quotecontroller.show'
            ) || (!empty($quote->assigned_to) && $quote->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Quote $quote
     * @return mixed
     */
    public function delete(User $user, Quote $quote)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $quote->user_id === $user->id || $user->hasPermissionTo(
                'quotecontroller.destroy'
            ) || (!empty($quote->assigned_to) && $quote->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Quote $quote
     * @return mixed
     */
    public function update(User $user, Quote $quote)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $quote->user_id === $user->id || $user->hasPermissionTo(
                'quotecontroller.update'
            ) || (!empty($quote->assigned_to) && $quote->assigned_to === $user->id);
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
                'quotecontroller.store'
            );
    }
}
