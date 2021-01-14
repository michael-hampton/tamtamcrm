<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Lead $lead
     * @return mixed
     */
    public function view(User $user, Lead $lead)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $lead->user_id === $user->id || $user->hasPermissionTo(
                'leadcontroller.show'
            ) || (!empty($lead->assigned_to) && $lead->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Lead $lead
     * @return mixed
     */
    public function update(User $user, Lead $lead)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $lead->user_id === $user->id || $user->hasPermissionTo(
                'leadcontroller.update'
            ) || (!empty($lead->assigned_to) && $lead->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Lead $lead
     * @return mixed
     */
    public function delete(User $user, Lead $lead)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $lead->user_id === $user->id || $user->hasPermissionTo(
                'leadcontroller.destroy'
            ) || (!empty($lead->assigned_to) && $lead->assigned_to === $user->id);
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
                'leadcontroller.store'
            );
    }
}
