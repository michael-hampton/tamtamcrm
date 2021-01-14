<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Invitation;
use App\Models\InvoiceInvitation;
use App\Models\User;
use Illuminate\Support\Str;

class InvitationFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return Invitation
     */
    public static function create(Account $account, User $user): Invitation
    {
        $ii = new Invitation();
        $ii->account_id = $account->id;
        $ii->user_id = $user->id;
        $ii->key = Str::random(20);

        return $ii;
    }

}
