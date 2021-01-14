<?php

namespace App\Transformations;

use App\Models\Promocode;
use App\Models\QuoteInvitation;

trait PromocodeTransformable
{

    /**
     * @param Promocode $promocode
     * @return array
     */
    public function transformPromocodes(Promocode $promocode)
    {
        return [
            'id'            => (int)$promocode->id,
            'code'          => (string)$promocode->code,
            'description'   => (string)$promocode->description,
            'amount_type'   => (string)$promocode->amount_type,
            'reward'        => (float)$promocode->reward,
            'quantity'      => (int)$promocode->quantity,
            'data'          => $promocode->data ?: '',
            'is_disposable' => (bool)$promocode->is_disposable ?: '',
            'expires_at'    => $promocode->expires_at ?: '',
            'scope'         => !empty($promocode->data['scope']) ? $promocode->data['scope'] : '',
            'scope_value'   => !empty($promocode->data['scope_value']) ? $promocode->data['scope_value'] : ''
        ];
    }

}
