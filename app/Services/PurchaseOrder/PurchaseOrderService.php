<?php

namespace App\Services\PurchaseOrder;

use App\Components\Pdf\PurchaseOrderPdf;
use App\Events\PurchaseOrder\PurchaseOrderChangeWasRequested;
use App\Events\PurchaseOrder\PurchaseOrderWasApproved;
use App\Events\PurchaseOrder\PurchaseOrderWasRejected;
use App\Factory\QuoteToRecurringPurchaseOrderFactory;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\RecurringPurchaseOrder;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Services\PurchaseOrder\MarkSent;
use App\Services\ServiceBase;
use Carbon\Carbon;
use ReflectionException;

class PurchaseOrderService extends ServiceBase
{
    /**
     * @var PurchaseOrder
     */
    protected PurchaseOrder $purchase_order;

    /**
     * PurchaseOrderService constructor.
     * @param PurchaseOrder $purchase_order
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        $config = [
            'email'   => $purchase_order->account->settings->should_email_purchase_order,
            'archive' => $purchase_order->account->settings->should_archive_purchase_order
        ];

        parent::__construct($purchase_order, $config);
        $this->purchase_order = $purchase_order;
    }

    public function approve(PurchaseOrderRepository $po_repo): ?PurchaseOrder
    {
        if ($this->purchase_order->status_id != PurchaseOrder::STATUS_SENT) {
            return null;
        }

        $this->purchase_order->setStatus(PurchaseOrder::STATUS_APPROVED);
        $this->purchase_order->date_approved = Carbon::now();
        $this->purchase_order->save();

        event(new PurchaseOrderWasApproved($this->purchase_order));

        // trigger
        $subject = trans('texts.purchase_order_approved_subject');
        $body = trans('texts.purchase_order_approved_body');
        $this->trigger($subject, $body, $po_repo);

        return $this->purchase_order;
    }

    /**
     * @param PurchaseOrderRepository $po_repo
     * @param array $data
     * @return PurchaseOrder|null
     */
    public function reject(PurchaseOrderRepository $po_repo, array $data = []): ?PurchaseOrder
    {
        if ($this->purchase_order->status_id != PurchaseOrder::STATUS_SENT) {
            return null;
        }

        if (!empty($data['public_notes'])) {
            $this->purchase_order->public_notes = $data['public_notes'];
        }

        $this->purchase_order->setStatus(PurchaseOrder::STATUS_REJECTED);
        $this->purchase_order->date_rejected = Carbon::now();
        $this->purchase_order->save();

        event(new PurchaseOrderWasRejected($this->purchase_order));

        // trigger
        $subject = trans('texts.purchase_order_rejected_subject');
        $body = trans('texts.purchase_order_rejected_body');
        $this->trigger($subject, $body, $po_repo);

        return $this->purchase_order;
    }

    /**
     * @param PurchaseOrderRepository $po_repo
     * @param array $data
     * @return PurchaseOrder|null
     */
    public function requestChange(PurchaseOrderRepository $po_repo, array $data = []): ?PurchaseOrder
    {
        if ($this->purchase_order->status_id != PurchaseOrder::STATUS_SENT) {
            return null;
        }

        if (!empty($data['public_notes'])) {
            $this->purchase_order->public_notes = $data['public_notes'];
        }

        $this->purchase_order->setStatus(PurchaseOrder::STATUS_CHANGE_REQUESTED);
        //$this->purchase_order->date_approved = Carbon::now();
        $this->purchase_order->save();

        event(new PurchaseOrderChangeWasRequested($this->purchase_order));

        // trigger
        $subject = trans('texts.purchase_order_change_requested_subject');
        $body = trans('texts.purchase_order_change_requested_body');
        $this->trigger($subject, $body, $po_repo);

        return $this->purchase_order;
    }

    /**
     * @param null $contact
     * @param bool $update
     * @return mixed|string
     * @throws ReflectionException
     */
    public function generatePdf($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->purchase_order->company->primary_contact()->first();
        }

        return CreatePdf::dispatchNow(
            (new PurchaseOrderPdf($this->purchase_order)),
            $this->purchase_order,
            $contact,
            $update,
            'purchase_order'
        );
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return PurchaseOrder|null
     */
    public function sendEmail($contact = null, $subject, $body, $template = 'purchase_order'): ?PurchaseOrder
    {
        if (!$this->sendInvitationEmails($subject, $body, $template, $contact)) {
            return null;
        }

        return $this->purchase_order;
    }

    /**
     * @return PurchaseOrder
     */
    public function calculateInvoiceTotals(): PurchaseOrder
    {
        return $this->calculateTotals($this->purchase_order);
    }

    /**
     * @param OrderRepository $order_repository
     * @return Order|null
     */
    public function convertQuoteToOrder(OrderRepository $order_repository)
    {
        return (new ConvertQuoteToOrder($this->quote, $order_repository))->execute();
    }

    /**
     * @param InvoiceRepository $invoice_repository
     * @return Invoice|null
     */
    public function convertQuoteToInvoice(InvoiceRepository $invoice_repository): ?Invoice
    {
        return (new ConvertQuoteToInvoice($this->quote, $invoice_repository))->execute();
    }

    /**
     * @param array $recurring
     * @return RecurringQuote|null
     */
    public function createRecurringQuote(array $recurring): ?RecurringQuote
    {
        if (empty($recurring)) {
            return null;
        }

        $arrRecurring['start_date'] = $recurring['start_date'];
        $arrRecurring['end_date'] = $recurring['end_date'];
        $arrRecurring['frequency'] = $recurring['frequency'];
        $arrRecurring['recurring_due_date'] = $recurring['recurring_due_date'];
        $recurringQuote = (new RecurringPurchaseOrderRepository(new RecurringPurchaseOrder))->save(
            $arrRecurring,
            PurchaseOrderToRecurringPurchaseOrderFactory::create($this->purchase_order)
        );

        $this->quote->recurring_purchase_order_id = $recurringQuote->id;
        $this->quote->save();

        return $recurringQuote;
    }
}
