<?php

namespace App\Policies;

use App\Models\CustomerGatewayToken;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerGatewayTokenPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param CustomerGatewayToken $customer_gateway_token
     * @return bool
     */
    public function view(User $user, CustomerGatewayToken $customer_gateway_token)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $customer_gateway_token->user_id === $user->id || $user->hasPermissionTo(
                'customergatewaytokencontroller.show'
            );
    }

    /**
     * @param User $user
     * @param CustomerGatewayToken $customer_gateway_token
     * @return bool
     */
    public function update(User $user, CustomerGatewayToken $customer_gateway_token)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $customer_gateway_token->user_id === $user->id || $user->hasPermissionTo(
                'customergatewaytokencontroller.update'
            );
    }

    /**
     * @param User $user
     * @param CustomerGatewayToken $customer_gateway_token
     * @return bool
     */
    public function delete(User $user, CustomerGatewayToken $customer_gateway_token)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $customer_gateway_token->user_id === $user->id || $user->hasPermissionTo(
                'customergatewaytokencontroller.destroy'
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
                'customergatewaytokencontroller.store'
            );
    }
}
