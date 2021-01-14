<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 29/02/2020
 * Time: 16:22
 */

namespace App\Transformations;


use App\Models\AccountUser;

class AccountUserTransformable
{
    public function transform(AccountUser $company_user)
    {
        return [
            // 'id' => $company_user->id,
            'account_id'    => $company_user->account_id,
            // 'user_id' => $company_user->user_id,
            // 'company_id' => $company_user->company_id,
            //'settings'      => $company_user->settings,
            'is_owner'      => (bool)$company_user->is_owner,
            'is_admin'      => (bool)$company_user->is_admin,
            'is_locked'     => (bool)$company_user->is_locked,
            'updated_at'    => (int)$company_user->updated_at,
            'archived_at'   => (int)$company_user->deleted_at,
            'created_at'    => (int)$company_user->created_at,
            'permissions'   => (object)$company_user->permissions,
            'notifications' => $company_user->notifications,
            'settings'      => (object)$company_user->settings,
        ];
    }

}
