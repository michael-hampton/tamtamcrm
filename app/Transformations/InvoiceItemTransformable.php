<?php

namespace App\Transformations;

trait InvoiceItemTransformable
{
    /**
     * Transform the invoice
     *
     * @param $item
     * @return array
     */
    protected function transform($item)
    {
        return [
            'id'                => (int)$item->id,
            'product_id'        => $item->product_id,
            'updated_at'        => $item->updated_at,
            'archived_at'       => $item->deleted_at,
            'notes'             => $item->notes ?: '',
            'unit_cost'         => (float)$item->unit_cost ?: '',
            'quantity'          => (float)($item->quantity ?: 0.0),
            'unit_tax'          => (float)($item->unit_tax ?: 0.0),
            'line_item_type_id' => (string)$item->line_item_type_id ?: '',
            'custom_value1'     => $item->custom_value1 ?: '',
            'custom_value2'     => $item->custom_value2 ?: '',
            'unit_discount'     => (float)$item->unit_discount ?: '',
        ];
    }
}
