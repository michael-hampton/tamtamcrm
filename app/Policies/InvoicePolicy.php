<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return mixed
     */
    public function view(User $user, Invoice $invoice)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $invoice->user_id === $user->id || $user->hasPermissionTo(
                'invoicecontroller.show'
            ) || (!empty($invoice->assigned_to) && $invoice->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return mixed
     */
    public function update(User $user, Invoice $invoice)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $invoice->user_id === $user->id || $user->hasPermissionTo(
                'invoicecontroller.update'
            ) || (!empty($invoice->assigned_to) && $invoice->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return mixed
     */
    public function delete(User $user, Invoice $invoice)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $invoice->user_id === $user->id || $user->hasPermissionTo(
                'invoicecontroller.destroy'
            ) || (!empty($invoice->assigned_to) && $invoice->assigned_to === $user->id);
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
                'invoicecontroller.store'
            );
    }
}
