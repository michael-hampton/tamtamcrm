<?php

namespace App\Transformations;

use App\Models\AccountUser;
use App\Models\User;

trait UserTransformable
{

    /**
     * @param User $user
     * @return array
     */
    protected function transformUser(User $user)
    {
        return [
            'id'              => (int)$user->id,
            'first_name'      => $user->first_name,
            'last_name'       => $user->last_name,
            'email'           => $user->email,
            'username'        => $user->username,
            'phone_number'    => $user->phone_number,
            'password'        => $user->password,
            'job_description' => $user->job_description,
            'account_users'   => $this->transformUserAccounts($user->account_users),
            'gender'          => $user->gender,
            'dob'             => $user->dob,
            'department'      => 0,
            'custom_value1'   => $user->custom_value1 ?: '',
            'custom_value2'   => $user->custom_value2 ?: '',
            'custom_value3'   => $user->custom_value3 ?: '',
            'custom_value4'   => $user->custom_value4 ?: '',
            'deleted_at'      => $user->deleted_at,
            'created_at'      => $user->created_at,
            'is_deleted'      => (bool)$user->is_deleted,
        ];
        /*if ($user->departments->count() > 0) {
            $objDepartment = $user->departments->first();
            $prop->department = $objDepartment->id;
            $prop->dept = $objDepartment->name;
        }

        return $prop;*/
    }

    private function transformUserAccounts($account_users)
    {
        if (empty($account_users)) {
            return [];
        }

        return $account_users->map(
            function (AccountUser $account_user) {
                return (new AccountUserTransformable())->transform($account_user);
            }
        )->all();
    }

}
