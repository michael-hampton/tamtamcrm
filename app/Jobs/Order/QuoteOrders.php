<?php

namespace App\Jobs\Order;

use App\Models\Order;
use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QuoteOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $quote;

    /**
     * Create a new job instance.
     *
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function handle()
    {
        if (empty($this->quote->task_id)) {
            return false;
        }

        $line_items = $this->quote->line_items;

        foreach ($line_items as $item) {
            $order = Order::whereId($item->order_id)->first();

            if ($order) {
                $order->status = Order::STATUS_QUOTED;
                $order->save();
            }
        }
    }
}
