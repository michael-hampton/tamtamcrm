<?php


namespace App\Factory;


use App\Models\Account;
use App\Models\CaseTemplate;
use App\Models\User;

class CaseTemplateFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return CaseTemplate
     */
    public static function create(Account $account, User $user): CaseTemplate
    {
        $template = new CaseTemplate;
        $template->account_id = $account->id;
        $template->user_id = $user->id;
        //$brand->status = 1;

        return $template;
    }
}
