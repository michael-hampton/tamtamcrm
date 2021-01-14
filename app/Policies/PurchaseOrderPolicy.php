<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param PurchaseOrder $purchaseOrder
     * @return mixed
     */
    public function view(User $user, PurchaseOrder $purchaseOrder)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $purchaseOrder->user_id === $user->id || $user->hasPermissionTo(
                'purchaseordercontroller.show'
            ) || (!empty($purchaseOrder->assigned_to) && $purchaseOrder->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param PurchaseOrder $purchaseOrder
     * @return mixed
     */
    public function update(User $user, PurchaseOrder $purchaseOrder)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $purchaseOrder->user_id === $user->id || $user->hasPermissionTo(
                'purchaseordercontroller.update'
            ) || (!empty($purchaseOrder->assigned_to) && $purchaseOrder->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param PurchaseOrder $purchaseOrder
     * @return mixed
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $purchaseOrder->user_id === $user->id || $user->hasPermissionTo(
                'purchaseordercontroller.destroy'
            ) || (!empty($purchaseOrder->assigned_to) && $purchaseOrder->assigned_to === $user->id);
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
                'purchaseordercontroller.store'
            );
    }
}
