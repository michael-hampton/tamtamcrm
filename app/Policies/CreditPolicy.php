<?php

namespace App\Policies;

use App\Models\Credit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Credit $credit
     * @return mixed
     */
    public function view(User $user, Credit $credit)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $credit->user_id === $user->id || $user->hasPermissionTo(
                'creditcontroller.show'
            ) || (!empty($credit->assigned_to) && $credit->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Credit $credit
     * @return mixed
     */
    public function update(User $user, Credit $credit)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $credit->user_id === $user->id || $user->hasPermissionTo(
                'creditcontroller.update'
            ) || (!empty($credit->assigned_to) && $credit->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Credit $credit
     * @return mixed
     */
    public function delete(User $user, Credit $credit)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $credit->user_id === $user->id || $user->hasPermissionTo(
                'creditcontroller.destroy'
            ) || (!empty($credit->assigned_to) && $credit->assigned_to === $user->id);
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
                'creditcontroller.store'
            );
    }
}
