<?php

namespace App\Transformations;

use App\Models\PaymentTerms;

trait PaymentTermsTransformable
{
    /**
     * @param PaymentTerms $payment_terms
     * @return array
     */
    protected function transformPaymentTerms(PaymentTerms $payment_terms)
    {
        return [
            'id'             => (int)$payment_terms->id,
            'created_at'     => $payment_terms->created_at,
            'deleted_at'     => $payment_terms->deleted_at,
            'name'           => (string)$payment_terms->name ?: '',
            'number_of_days' => (int)$payment_terms->number_of_days ?: 1,
        ];
    }
}
