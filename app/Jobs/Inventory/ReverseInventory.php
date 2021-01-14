<?php

namespace App\Jobs\Inventory;

use App\Models\Credit;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Capsule\Eloquent;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class UpdateInventory
 * @package App\Jobs\Inventory
 */
class ReverseInventory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Credit
     */
    private $entity;

    /**
     * @var bool
     */
    private $restore_reserved_stock = false;


    /**
     * ReverseInventory constructor.
     * @param $entity
     * @param bool $restore_reserved_stock
     */
    public function __construct($entity, $restore_reserved_stock = false)
    {
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->entity->line_items)) {
            return;
        }

        foreach ($this->entity->line_items as $item) {
            if (empty($item->product_id) || $item->type_id !== 1) {
                continue;
            }

            if (!empty($item->attribute_id)) {
                $product_attribute = ProductAttribute::find($item->attribute_id);

                if ($this->entity->customer->getSetting('should_update_inventory')) {
                    $product_attribute->increaseQuantityAvailiable($item->quantity);

                    if ($this->restore_reserved_stock) {
                        $product_attribute->reduceQuantityReserved($item->quantity);
                    }
                }

                continue;
            }

            $product = Product::where('id', $item->product_id)->first();

            if (empty($product) || $product->count() === 0) {
                continue;
            }

            if ($this->entity->customer->getSetting('should_update_inventory')) {
                $product->increaseQuantityAvailiable($item->quantity);

                if ($this->restore_reserved_stock) {
                    $product->reduceQuantityReserved($item->quantity);
                }
            }
        }
    }
}