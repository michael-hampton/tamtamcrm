<?php

namespace App\Transformations;

use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Paymentable;

class PaymentableTransformer
{
    /**
     * @param Paymentable $paymentable
     * @return array
     */
    public function transform(Paymentable $paymentable)
    {
        if ($paymentable->paymentable_type == Credit::class) {
            $entity_key = 'credit_id';
            $entity = Credit::whereId($paymentable->paymentable_id)->first();
        } else {
            $entity_key = 'invoice_id';
            $entity = Invoice::whereId($paymentable->paymentable_id)->first();
        }

        return [
            'id'               => $paymentable->id,
            $entity_key        => $paymentable->paymentable_id,
            'amount'           => $paymentable->amount,
            'number'           => !empty($entity) ? $entity->number : null,
            'date'             => $paymentable->payment->date,
            'paymentable_type' => $paymentable->paymentable_type,
            'refunded'         => (float)$paymentable->refunded,
            'payment_id'       => (int)$paymentable->payment_id
        ];
    }
}
