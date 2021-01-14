<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAttribute;

class CheckStock
{
    /**
     * @var Order
     */
    private Order $order;

    /**
     * @var bool
     */
    private bool $allow_partial_orders;

    private bool $allow_backorders;

    /**
     * CheckStock constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->allow_partial_orders = $this->order->customer->getSetting('allow_partial_orders');
        $this->allow_backorders = $order->customer->getSetting(
            'allow_backorders'
        );
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $no_stock = [];
        $this->order->setStatus(Order::STATUS_DRAFT);

        if (empty($this->order->line_items)) {
            return $this->order;
        }

        foreach ($this->order->line_items as $item) {
            $type_id = isset($item->type_id) ? $item->type_id : 1;

            if (empty($item->product_id) || $type_id !== 1) {
                continue;
            }

            if (!empty($item->attribute_id)) {
                $product_attribute = ProductAttribute::find($item->attribute_id);

                if (empty($product_attribute) || $product_attribute->count() === 0) {
                    continue;
                }

                $quantity_availiable = $product_attribute->quantity - $product_attribute->reserved_stock;

                if ($quantity_availiable <= 0 || $item->quantity > $quantity_availiable) {
                    if ($this->allow_partial_orders) {
                        $item->status_id = Order::STATUS_BACKORDERED;
                    }

                    $no_stock[] = $product_attribute->id;
                    continue;
                }

                continue;
            }

            $product = Product::where('id', $item->product_id)->first();

            if (empty($product) || $product->count() === 0) {
                continue;
            }

            $quantity_availiable = $product->quantity - $product->reserved_stock;

            if ($quantity_availiable <= 0 || $item->quantity > $quantity_availiable) {
                if ($this->allow_partial_orders) {
                    $item->status_id = Order::STATUS_BACKORDERED;
                }

                $no_stock[] = $product->id;
            }
        }

        if ((count($no_stock) > 1 && !$this->allow_partial_orders) || count(
                $no_stock
            ) > 0 && !$this->allow_backorders) {
            return null;
        }

        if (count($no_stock) > 0 && !$this->allow_partial_orders) {
            $this->order->setStatus(Order::STATUS_BACKORDERED);
        }

        if (count($no_stock) > 0 && $this->allow_partial_orders) {
            $this->order->setStatus(Order::STATUS_PARTIAL);
        }

        return $this->order;
    }
}
