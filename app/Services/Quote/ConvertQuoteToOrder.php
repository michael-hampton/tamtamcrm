<?php

namespace App\Services\Quote;

use App\Factory\CloneQuoteToOrderFactory;
use App\Models\Order;
use App\Models\Quote;
use App\Repositories\OrderRepository;

/**
 * Class ConvertQuote
 * @package App\Services\Quote
 */
class ConvertQuoteToOrder
{
    /**
     * @var Quote
     */
    private Quote $quote;

    /**
     * @var OrderRepository
     */
    private OrderRepository $order_repo;

    /**
     * ConvertQuoteToOrder constructor.
     * @param Quote $quote
     * @param OrderRepository $order_repository
     */
    public function __construct(Quote $quote, OrderRepository $order_repository)
    {
        $this->quote = $quote;
        $this->order_repo = $order_repository;
    }

    /**
     * @return Order|null
     */
    public function execute(): ?Order
    {
        if (!empty($this->quote->order_id) || $this->quote->status_id === Quote::STATUS_EXPIRED) {
            return null;
        }

        $order = CloneQuoteToOrderFactory::create(
            $this->quote,
            $this->quote->user,
            $this->quote->account
        );

        $order = $this->order_repo->save(
            [
                'status_id' => Order::STATUS_DRAFT
            ],
            $order
        );

        $this->quote->setOrderId($order->id);
        $this->quote->save();

        return $order;
    }
}
