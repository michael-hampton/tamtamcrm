<?php

namespace App\Factory;

use App\Models\Credit;
use App\Models\User;

class CloneCreditFactory
{
    /**
     * @param $credit
     * @param User $user
     * @return Credit
     */
    public static function create($credit, User $user): Credit
    {
        $clone_credit = new Credit();
        $clone_credit->fill($credit->toArray());
        $clone_credit->number = null;
        $clone_credit->setStatus(Credit::STATUS_DRAFT);
        $clone_credit->setCustomer($credit->customer);
        $clone_credit->setUser($user);
        $clone_credit->setBalance($credit->total);
        $clone_credit->setAccount($credit->account);
        $clone_credit->setDueDate();
        $clone_credit->setNumber();

        return $clone_credit;
    }
}
