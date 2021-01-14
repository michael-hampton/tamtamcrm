<?php

namespace App\Services\RecurringQuote;

use App\Components\Pdf\InvoicePdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\RecurringQuote;
use App\Services\Customer\CustomerService;
use App\Services\Invoice\ApplyNumber;
use App\Services\Invoice\ApplyPayment;
use App\Services\Invoice\CreateInvitations;
use App\Services\Invoice\HandleCancellation;
use App\Services\Invoice\HandleReversal;
use App\Services\Invoice\MarkSent;
use App\Services\Invoice\UpdateBalance;
use App\Services\ServiceBase;

class RecurringQuoteService extends ServiceBase
{
    /**
     * @var RecurringQuote
     */
    protected RecurringQuote $quote;

    /**
     * RecurringQuoteService constructor.
     * @param RecurringQuote $quote
     */
    public function __construct(RecurringQuote $quote)
    {
        parent::__construct($quote);
        $this->quote = $quote;
    }

    public function generatePdf($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->quote->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new InvoicePdf($this->quote)), $this->quote, $contact, $update, 'quote');
    }

    public function calculateInvoiceTotals(): RecurringQuote
    {
        return $this->calculateTotals($this->quote);
    }
}
