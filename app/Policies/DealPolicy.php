<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DealPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Deal $deal
     * @return mixed
     */
    public function view(User $user, Deal $deal)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $deal->user_id === $user->id || $user->hasPermissionTo(
                'dealcontroller.show'
            ) || (!empty($deal->assigned_to) && $deal->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Deal $deal
     * @return mixed
     */
    public function update(User $user, Deal $deal)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $deal->user_id === $user->id || $user->hasPermissionTo(
                'dealcontroller.update'
            ) || (!empty($deal->assigned_to) && $deal->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Deal $deal
     * @return mixed
     */
    public function delete(User $user, Deal $deal)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $deal->user_id === $user->id || $user->hasPermissionTo(
                'dealcontroller.destroy'
            ) || (!empty($deal->assigned_to) && $deal->assigned_to === $user->id);
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
                'dealcontroller.store'
            );
    }

}
