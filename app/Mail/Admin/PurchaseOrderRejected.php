<?php

namespace App\Mail\Admin;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderRejected extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var PurchaseOrder
     */
    private PurchaseOrder $purchase_order;

    /**
     * PurchaseOrderApproved constructor.
     * @param PurchaseOrder $purchase_order
     * @param User $user
     */
    public function __construct(PurchaseOrder $purchase_order, User $user)
    {
        parent::__construct('purchase_order_rejected', $purchase_order);

        $this->purchase_order = $purchase_order;
        $this->entity = $purchase_order;
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
            'total' => $this->purchase_order->getFormattedTotal(),
            'quote' => $this->purchase_order->getNumber(),
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => $this->getUrl() . 'purchase_orders/' . $this->purchase_order->id,
            'button_text' => trans('texts.view_purchase_order'),
        ];
    }
}
