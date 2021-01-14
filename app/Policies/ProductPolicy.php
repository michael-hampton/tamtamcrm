<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Product $product
     * @return mixed
     */
    public function view(User $user, Product $product)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $product->user_id === $user->id || $user->hasPermissionTo(
                'productcontroller.show'
            ) || (!empty($product->assigned_to) && $product->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Product $product
     * @return mixed
     */
    public function update(User $user, Product $product)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $product->user_id === $user->id || $user->hasPermissionTo(
                'productcontroller.update'
            ) || (!empty($product->assigned_to) && $product->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Product $product
     * @return mixed
     */
    public function delete(User $user, Product $product)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $product->user_id === $user->id || $user->hasPermissionTo(
                'productcontroller.destroy'
            ) || (!empty($product->assigned_to) && $product->assigned_to === $user->id);
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
                'productcontroller.store'
            );
    }
}
