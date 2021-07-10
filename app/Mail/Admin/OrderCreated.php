<?php

namespace App\Mail\Admin;

use App\Models\Order;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * OrderCreated constructor.
     * @param Order $order
     * @param User $user
     */
    public function __construct(Order $order, User $user)
    {
        parent::__construct('order_created', $order);

        $this->order = $order;
        $this->entity = $order;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return void
     */
    public function build()
    {
        $data = $this->getData();

        $this->setSubject($data);
        $this->setMessage($data);
        $this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'    => $this->order->getFormattedTotal(),
            'customer' => (new CustomerViewModel($this->order->customer))->name(),
            'order'    => $this->order->getNumber(),
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => $this->getUrl() . 'orders/' . $this->order->id,
            'button_text' => trans('texts.view_order'),
        ];
    }
}
