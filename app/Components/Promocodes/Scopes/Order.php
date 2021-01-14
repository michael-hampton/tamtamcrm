<?php

namespace App\Components\Promocodes\Scopes;

class Order extends BaseScope implements ScopeInterface
{

    public function validate(): bool
    {
        if ($this->order->total < $this->scope_value) {
            return false;
        }

        return true;
    }
}