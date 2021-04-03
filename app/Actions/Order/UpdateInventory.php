<?php

namespace App\Actions\Order;


use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;

class UpdateInventory
{

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }


    public function updateInventory($line_items, $data)
    {
        if (empty($data['line_items'])) {
            return true;
        }

        $new_lines = collect($data['line_items'])->keyBy('product_id')->toArray();

        foreach ($line_items as $line_item) {
            if ($line_item->type_id !== Invoice::PRODUCT_TYPE) {
                continue;
            }

            $product = Product::where('id', '=', $line_item->product_id)->first();

            if (empty($new_lines[$line_item->product_id])) {
                $difference = $line_item->quantity;
                $product->increment('quantity', $difference);
                $product->save();
                continue;
            }

            $new_line = $new_lines[$line_item->product_id];

            if ($new_line->quantity > $line_item->quantity) {
                $difference = $new_line->quantity - $line_item->quantity;
                $product->decrement('quantity', $difference);
                $product->save();
            }

            if ($new_line->quantity < $line_item->quantity) {
                $difference = $line_item->quantity - $new_line->quantity;
                $product->increment('quantity', $difference);
                $product->save();
            }
        }

        return true;
    }
}