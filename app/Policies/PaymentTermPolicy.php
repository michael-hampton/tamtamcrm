<?php

namespace App\Policies;

use App\Models\PaymentTerms;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentTermPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param PaymentTerms $payment_terms
     * @return mixed
     */
    public function view(User $user, PaymentTerms $payment_terms)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $payment_terms->user_id === $user->id || $user->hasPermissionTo(
                'paymenttermscontroller.show'
            );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param PaymentTerms $payment_terms
     * @return mixed
     */
    public function delete(User $user, PaymentTerms $payment_terms)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $payment_terms->user_id === $user->id || $user->hasPermissionTo(
                'paymenttermscontroller.destroy'
            );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param PaymentTerms $payment_terms
     * @return mixed
     */
    public function update(User $user, PaymentTerms $payment_terms)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $payment_terms->user_id === $user->id || $user->hasPermissionTo(
                'paymenttermscontroller.update'
            );
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
                'paymenttermscontroller.store'
            );
    }
}
