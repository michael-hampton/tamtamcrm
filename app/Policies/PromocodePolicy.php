<?php

namespace App\Policies;

use App\Models\Promocode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromocodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Promocode $promocode
     * @return mixed
     */
    public function view(User $user, Promocode $promocode)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $promocode->user_id === $user->id || $user->hasPermissionTo('promocodecontroller.show');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Promocode $promocode
     * @return mixed
     */
    public function delete(User $user, Promocode $promocode)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $promocode->user_id === $user->id || $user->hasPermissionTo('promocodecontroller.destroy');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Promocode $promocode
     * @return mixed
     */
    public function update(User $user, Promocode $promocode)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $promocode->user_id === $user->id || $user->hasPermissionTo('promocodecontroller.update');
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
                'promocodecontroller.store'
            );
    }
}
