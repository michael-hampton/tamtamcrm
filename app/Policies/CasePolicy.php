<?php

namespace App\Policies;

use App\Models\Cases;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CasePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Cases $case
     * @return mixed
     */
    public function view(User $user, Cases $case)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $case->user_id === $user->id || $user->hasPermissionTo(
                'casecontroller.index'
            ) || (!empty($case->assigned_to) && $case->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Cases $case
     * @return mixed
     */
    public function update(User $user, Cases $case)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $case->user_id === $user->id || $user->hasPermissionTo(
                'casecontroller.update'
            ) || (!empty($case->assigned_to) && $case->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Cases $case
     * @return mixed
     */
    public function delete(User $user, Cases $case)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $case->user_id === $user->id || $user->hasPermissionTo(
                'casecontroller.destroy'
            ) || (!empty($case->assigned_to) && $case->assigned_to === $user->id);
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
                'casecontroller.store'
            );
    }
}
