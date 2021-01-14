<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Services\ServiceBase;

class TransactionService extends ServiceBase
{

    private $entity;

    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->entity = $entity;
    }

    public function createTransaction($amount, $new_balance, $notes = '')
    {
        $transaction = new Transaction;
        $transaction->setAccount($this->entity->account);
        $transaction->setUser($this->entity->user);
        $transaction->setCustomer($this->entity->customer);
        $transaction->setUpdatedBalance($new_balance);
        $transaction->setOriginalBalance();
        $transaction->setAmount($amount);
        $transaction->setNotes($notes);

        $this->entity->transactions()->save($transaction);

        return $transaction;
    }
}
