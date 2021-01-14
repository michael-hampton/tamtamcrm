<?php


namespace App\Transformations;


use App\Models\Transaction;

class TransactionTransformable
{
    /**
     * @param Transaction $transaction
     * @return array
     */
    public function transformTransaction(Transaction $transaction)
    {
        return [
            'id'              => (int)$transaction->id,
            'entity_number'   => $transaction->getEntity()->number,
            'entity_name'     => str_replace('App\Models\\', '', $transaction->transactionable_type),
            'notes'           => (string)$transaction->notes ?: '',
            'created_at'      => (string)$transaction->created_at ?: '',
            'updated_balance' => (float)$transaction->updated_balance,
            'amount'          => (float)$transaction->amount,
        ];
    }
}