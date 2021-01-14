<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param BankAccount $bank_account
     * @return mixed
     */
    public function view(User $user, BankAccount $bank_account)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $bank_account->user_id === $user->id || $user->hasPermissionTo(
                'bankaccountcontroller.index'
            ) || (!empty($bank_account->assigned_to) && $bank_account->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param BankAccount $bank_account
     * @return mixed
     */
    public function update(User $user, BankAccount $bank_account)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $bank_account->user_id === $user->id || $user->hasPermissionTo(
                'bankaccountcontroller.update'
            ) || (!empty($bank_account->assigned_to) && $bank_account->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param BankAccount $bank_account
     * @return mixed
     */
    public function delete(User $user, BankAccount $bank_account)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $bank_account->user_id === $user->id || $user->hasPermissionTo(
                'bankaccountcontroller.destroy'
            ) || (!empty($bank_account->assigned_to) && $bank_account->assigned_to === $user->id);
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
                'bankaccountcontroller.store'
            );
    }
}
