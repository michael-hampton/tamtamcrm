<?php

namespace App\Jobs\Product;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ProductRepository $product_repo;

    private Product $product;

    private array $data;

    /**
     * Create a new job instance.
     *
     * @param ProductRepository $product_repo
     * @param array $data
     * @param Product $product
     */
    public function __construct(ProductRepository $product_repo, array $data, Product $product)
    {
        $this->product_repo = $product_repo;
        $this->data = $data;
        $this->product;
    }

    public function handle()
    {
        $this->product->service()->createProduct($this->product_repo, $this->data);
    }
}
