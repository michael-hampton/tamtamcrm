<?php

namespace App\Transformations;

use App\Models\Account;
use stdClass;

trait AccountTransformable
{

    /**
     * @param Account $account
     * @return array
     */
    public function transformAccount(Account $account)
    {
        $std = new stdClass;

        return [
            'id'                => $account->id,
            'custom_fields'     => $account->custom_fields ?: $std,
            'subdomain'         => (string)$account->subdomain ?: '',
            'portal_domain'     => (string)$account->portal_domain ?: '',
            'settings'          => $account->settings ?: '',
            'deleted_at'        => $account->deleted_at,
            'slack_webhook_url' => $account->slack_webhook_url,
            'file_count'        => $account->files->count()
        ];
    }

}
