<?php

namespace App\Jobs\Product;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Capsule\Eloquent;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $line_items;


    /**
     * Create a new job instance.
     *
     * @param $line_items
     */
    public function __construct($line_items)
    {
        $this->line_items = $line_items;
    }

    /**
     * Execute the job.
     *
     *
     * @return bool
     */
    public function handle()
    {
        if (empty($this->line_items)) {
            return true;
        }

        foreach ($this->line_items as $item) {
            if (empty($item->product_id) || $item->type_id !== 1) {
                continue;
            }

            $product = Product::find($item->product_id);

            if (!$product) {
                continue;
            }

            $product->price = $item->unit_price;
            $product->save();
        }
    }
}