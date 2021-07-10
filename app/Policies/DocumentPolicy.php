<?php

namespace App\Policies;


use App\Models\File;
use App\Models\User;

class DocumentPolicy
{

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $user->hasPermissionTo(
                'uploadcontroller.store'
            );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param File $file
     * @return bool
     */
    public function delete(User $user, File $file)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $file->user_id === $user->id || $user->hasPermissionTo(
                'uploadcontroller.destroy'
            );
    }

    public function view(User $user, File $file)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $file->user_id === $user->id || $user->hasPermissionTo(
                'uploadcontroller.index'
            );
    }
}