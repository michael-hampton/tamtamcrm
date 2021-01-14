<?php

namespace App\Components\Promocodes\Scopes;

class Product extends BaseScope implements ScopeInterface
{
    public function validate(): bool
    {
        if (!in_array($this->scope_value, array_column($this->order->line_items, 'product_id'))) {
            return false;
        }

        return true;
    }
}