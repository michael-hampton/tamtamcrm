<?php

namespace App\Transformations;

use App\Models\Account;
use stdClass;

trait AccountTransformable
{

    /**
     * @param Account $account
     * @return Account
     */
    public function transformAccount(Account $account)
    {
        $obj = new Account;
        $obj->id = (int)$account->id;
        $std = new stdClass;

        $obj->custom_surcharge_taxes1 = (bool)$account->custom_surcharge_taxes1;
        $obj->custom_surcharge_taxes2 = (bool)$account->custom_surcharge_taxes2;
        $obj->custom_surcharge_taxes3 = (bool)$account->custom_surcharge_taxes3;
        $obj->custom_surcharge_taxes4 = (bool)$account->custom_surcharge_taxes4;
        $obj->custom_fields = $account->custom_fields ?: $std;
        $obj->size_id = (string)$account->size_id ?: '';
        $obj->industry_id = (string)$account->industry_id ?: '';
        $obj->subdomain = (string)$account->subdomain ?: '';
        $obj->portal_domain = (string)$account->portal_domain ?: '';
        $obj->settings = $account->settings ?: '';
        $obj->updated_at = (int)$account->updated_at;
        $obj->deleted_at = (int)$account->deleted_at;
        $obj->slack_webhook_url = (string)$account->slack_webhook_url;
        return $obj;
    }

}
