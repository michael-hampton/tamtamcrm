<?php

namespace App\Mail\Admin;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderApproved extends AdminMailer
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
        parent::__construct('purchase_order_approved', $purchase_order);

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
        $this->execute($this->buildMessage());
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
    private function buildMessage(): array
    {
        return [
            'title'       => $this->subject,
            'body'        => $this->message,
            'url'         => $this->getUrl() . 'purchase_orders/' . $this->purchase_order->id,
            'button_text' => trans('texts.view_purchase_order'),
            'signature'   => !empty($this->settings) ? $this->settings->email_signature : '',
            'logo'        => (new AccountViewModel($this->purchase_order->account))->logo()
        ];
    }
}
