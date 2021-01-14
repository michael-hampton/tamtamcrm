<?php

namespace App\Policies;

use App\Models\RecurringQuote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringQuotePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param RecurringQuote $recurringQuote
     * @return mixed
     */
    public function view(User $user, RecurringQuote $recurringQuote)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $recurringQuote->user_id === $user->id || $user->hasPermissionTo(
                'recurringquotecontroller.show'
            ) || (!empty($recurringQuote->assigned_to) && $recurringQuote->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param RecurringQuote $recurringQuote
     * @return mixed
     */
    public function delete(User $user, RecurringQuote $recurringQuote)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $recurringQuote->user_id === $user->id || $user->hasPermissionTo(
                'recurringquotecontroller.destroy'
            ) || (!empty($recurringQuote->assigned_to) && $recurringQuote->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param RecurringQuote $recurringQuote
     * @return mixed
     */
    public function update(User $user, RecurringQuote $recurringQuote)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $recurringQuote->user_id === $user->id || $user->hasPermissionTo(
                'recurringquotecontroller.update'
            ) || (!empty($recurringQuote->assigned_to) && $recurringQuote->assigned_to === $user->id);
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
                'recurringquotecontroller.store'
            );
    }
}
