<?php

namespace App\Mail\Admin;

use App\Models\Deal;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Traits\Money;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CompanyViewModel;
use App\ViewModels\CustomerViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderCreated extends AdminMailer
{
    use Queueable, SerializesModels, Money;

    /**
     * @var PurchaseOrder
     */
    private PurchaseOrder $purchase_order;

    /**
     * EntityCreated constructor.
     * @param $entity
     * @param $entity_string
     * @param User $user
     */
    public function __construct(PurchaseOrder $purchase_order, User $user)
    {
        parent::__construct('purchase_order_created', $purchase_order);

        $this->purchase_order = $purchase_order;
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
            'total'          => $this->purchase_order->getFormattedTotal(),
            'purchase_order' => $this->purchase_order->getNumber(),
            'company'        => (new CompanyViewModel($this->purchase_order->company))->name(),
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
