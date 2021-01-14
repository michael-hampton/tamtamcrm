<?php

namespace App\Policies;

use App\Models\RecurringInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringInvoicePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param RecurringInvoice $recurringInvoice
     * @return mixed
     */
    public function view(User $user, RecurringInvoice $recurringInvoice)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $recurringInvoice->user_id === $user->id || $user->hasPermissionTo(
                'recurringinvoicecontroller.show'
            ) || (!empty($recurringInvoice->assigned_to) && $recurringInvoice->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param RecurringInvoice $recurringInvoice
     * @return mixed
     */
    public function delete(User $user, RecurringInvoice $recurringInvoice)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $recurringInvoice->user_id === $user->id || $user->hasPermissionTo(
                'recurringinvoicecontroller.destroy'
            ) || (!empty($recurringInvoice->assigned_to) && $recurringInvoice->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param RecurringInvoice $recurringInvoice
     * @return mixed
     */
    public function update(User $user, RecurringInvoice $recurringInvoice)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $recurringInvoice->user_id === $user->id || $user->hasPermissionTo(
                'recurringinvoicecontroller.update'
            ) || (!empty($recurringInvoice->assigned_to) && $recurringInvoice->assigned_to === $user->id);
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
                'recurringinvoicecontroller.store'
            );
    }
}
