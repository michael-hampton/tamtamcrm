<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Company $company
     * @return mixed
     */
    public function view(User $user, Company $company)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $company->user_id === $user->id || $user->hasPermissionTo(
                'companycontroller.show'
            ) || (!empty($company->assigned_to) && $company->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Company $company
     * @return mixed
     */
    public function update(User $user, Company $company)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $company->user_id === $user->id || $user->hasPermissionTo(
                'companycontroller.update'
            ) || (!empty($company->assigned_to) && $company->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Company $company
     * @return mixed
     */
    public function delete(User $user, Company $company)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $company->user_id === $user->id || $user->hasPermissionTo(
                'companycontroller.destroy'
            ) || (!empty($company->assigned_to) && $company->assigned_to === $user->id);
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
                'companycontroller.store'
            );
    }
}
