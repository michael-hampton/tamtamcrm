<?php

namespace App\Services\RecurringInvoice;

use App\Components\Pdf\InvoicePdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\RecurringInvoice;
use App\Services\Customer\CustomerService;
use App\Services\Invoice\ApplyNumber;
use App\Services\Invoice\ApplyPayment;
use App\Services\Invoice\CreateInvitations;
use App\Services\Invoice\HandleCancellation;
use App\Services\Invoice\HandleReversal;
use App\Services\Invoice\MarkSent;
use App\Services\Invoice\UpdateBalance;
use App\Services\ServiceBase;

class RecurringInvoiceService extends ServiceBase
{
    protected $invoice;

    /**
     * RecurringInvoiceService constructor.
     * @param RecurringInvoice $invoice
     */
    public function __construct(RecurringInvoice $invoice)
    {
        parent::__construct($invoice);
        $this->invoice = $invoice;
    }

    public function generatePdf($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->invoice->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new InvoicePdf($this->invoice)), $this->invoice, $contact, $update, 'invoice');
    }

    public function calculateInvoiceTotals(): RecurringInvoice
    {
        return $this->calculateTotals($this->invoice);
    }
}
