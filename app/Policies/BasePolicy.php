<?php


namespace App\Policies;


use App\Models\User;

class BasePolicy
{
    public function restore(User $user, $entity)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $entity->user_id === $user->id || (!empty($entity->assigned_to) && $entity->assigned_to === $user->id);
    }


}
