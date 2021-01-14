<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Payment $payment
     * @return mixed
     */
    public function view(User $user, Payment $payment)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $payment->user_id === $user->id || (!empty($payment->assigned_to) && $payment->assigned_to === $user->id) || $user->hasPermissionTo(
                'paymentcontroller.show'
            );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Payment $payment
     * @return mixed
     */
    public function update(User $user, Payment $payment)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $payment->user_id === $user->id || $user->hasPermissionTo(
                'paymentcontroller.update'
            ) || (!empty($payment->assigned_to) && $payment->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Payment $payment
     * @return mixed
     */
    public function delete(User $user, Payment $payment)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $payment->user_id === $user->id || $user->hasPermissionTo(
                'paymentcontroller.destroy'
            ) || (!empty($payment->assigned_to) && $payment->assigned_to === $user->id);
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
                'paymentcontroller.store'
            );
    }
}
