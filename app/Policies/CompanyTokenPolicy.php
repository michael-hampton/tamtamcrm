<?php

namespace App\Policies;

use App\Models\CompanyToken;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyTokenPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param CompanyToken $token
     * @return bool
     */
    public function view(User $user, CompanyToken $token)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $token->user_id === $user->id || $user->hasPermissionTo(
                'tokencontroller.show'
            );
    }

    /**
     * @param User $user
     * @param CompanyToken $token
     * @return bool
     */
    public function delete(User $user, CompanyToken $token)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $token->user_id === $user->id || $user->hasPermissionTo(
                'tokencontroller.destroy'
            );
    }

    /**
     * @param User $user
     * @param CompanyToken $token
     * @return bool
     */
    public function update(User $user, CompanyToken $token)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $token->user_id === $user->id || $user->hasPermissionTo(
                'tokencontroller.update'
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
                'tokencontroller.store'
            );
    }
}
