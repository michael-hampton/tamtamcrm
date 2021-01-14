<?php

namespace App\Policies;

use App\Models\CompanyGateway;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyGatewayPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param CompanyGateway $companyGateway
     * @return mixed
     */
    public function view(User $user, CompanyGateway $companyGateway)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $companyGateway->user_id === $user->id || $user->hasPermissionTo(
                'companygatewaycontroller.show'
            ) || (!empty($companyGateway->assigned_to) && $companyGateway->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param CompanyGateway $companyGateway
     * @return mixed
     */
    public function update(User $user, CompanyGateway $companyGateway)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $companyGateway->user_id === $user->id || $user->hasPermissionTo(
                'companygatewaycontroller.update'
            ) || (!empty($companyGateway->assigned_to) && $companyGateway->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param CompanyGateway $companyGateway
     * @return mixed
     */
    public function delete(User $user, CompanyGateway $companyGateway)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $companyGateway->user_id === $user->id || $user->hasPermissionTo(
                'companygatewaycontroller.destroy'
            ) || (!empty($companyGateway->assigned_to) && $companyGateway->assigned_to === $user->id);
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
                'companygatewaycontroller.store'
            );
    }
}
