<?php

namespace App\Transformations;

use App\Models\BankAccount;

trait BankAccountTransformable
{

    /**
     * @param BankAccount $bank_account
     * @return array
     */
    protected function transformBankAccount(BankAccount $bank_account)
    {
        return [
            'id'            => (int)$bank_account->id,
            'bank_id'       => (int)$bank_account->bank_id,
            'name'          => $bank_account->name,
            'username'      => $bank_account->username,
            'private_notes' => $bank_account->private_notes,
            'public_notes'  => $bank_account->public_notes,
            'user_id'       => (int)$bank_account->user_id,
            'assigned_to'   => (int)$bank_account->assigned_to,
            'bank'          => $bank_account->bank,
            'is_deleted'    => (bool)$bank_account->is_deleted,
        ];
    }

}
