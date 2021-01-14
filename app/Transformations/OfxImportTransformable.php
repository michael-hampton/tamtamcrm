<?php

namespace App\Transformations;

use OfxParser\Entities\Transaction;

trait OfxImportTransformable
{
    /**
     * @param Transaction $transaction
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        return [
            'type'              => $transaction->type,
            'date'              => $transaction->date->format('d-m-Y'),
            'userInitiatedDate' => $transaction->userInitiatedDate->format('d-m-Y'),
            'amount'            => abs($transaction->amount),
            'uniqueId'          => $transaction->uniqueId,
            'id'                => $transaction->uniqueId,
            'name'              => $transaction->name,
            'memo'              => $transaction->memo
        ];
    }
}
