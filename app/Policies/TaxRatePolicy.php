<?php

namespace App\Policies;

use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxRatePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param TaxRate $taxRate
     * @return mixed
     */
    public function view(User $user, TaxRate $taxRate)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $taxRate->user_id === $user->id || $user->hasPermissionTo(
                'taxratecontroller.show'
            ) || (!empty($taxRate->assigned_to) && $taxRate->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param TaxRate $taxRate
     * @return mixed
     */
    public function delete(User $user, TaxRate $taxRate)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $taxRate->user_id === $user->id || $user->hasPermissionTo(
                'taxratecontroller.destroy'
            ) || (!empty($taxRate->assigned_to) && $taxRate->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param TaxRate $taxRate
     * @return mixed
     */
    public function update(User $user, TaxRate $taxRate)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $taxRate->user_id === $user->id || $user->hasPermissionTo(
                'taxratecontroller.update'
            ) || (!empty($taxRate->assigned_to) && $taxRate->assigned_to === $user->id);
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
                'taxratecontroller.store'
            );
    }
}
