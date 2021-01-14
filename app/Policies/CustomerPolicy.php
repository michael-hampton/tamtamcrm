<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function view(User $user, Customer $customer)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $customer->user_id === $user->id || $user->hasPermissionTo(
                'customercontroller.show'
            ) || (!empty($customer->assigned_to) && $customer->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function update(User $user, Customer $customer)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $customer->user_id === $user->id || $user->hasPermissionTo(
                'customercontroller.update'
            ) || (!empty($customer->assigned_to) && $customer->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function delete(User $user, Customer $customer)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $customer->user_id === $user->id || $user->hasPermissionTo(
                'customercontroller.destroy'
            ) || (!empty($customer->assigned_to) && $customer->assigned_to === $user->id);
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
                'customercontroller.store'
            );
    }
}
