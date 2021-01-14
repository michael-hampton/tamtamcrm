<?php

namespace App\Services\Quote;

use App\Components\Pdf\InvoicePdf;
use App\Events\Quote\PurchaseOrderWasApproved;
use App\Events\Quote\QuoteChangeWasRequested;
use App\Events\Quote\QuoteWasApproved;
use App\Events\Quote\QuoteWasRejected;
use App\Factory\QuoteToRecurringQuoteFactory;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\RecurringQuoteRepository;
use App\Services\Quote\MarkSent;
use App\Services\ServiceBase;
use Carbon\Carbon;
use ReflectionException;

class QuoteService extends ServiceBase
{
    /**
     * @var Quote
     */
    protected Quote $quote;

    /**
     * QuoteService constructor.
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $config = [
            'email'   => $quote->customer->getSetting('should_email_quote'),
            'archive' => $quote->customer->getSetting('should_archive_quote')
        ];

        parent::__construct($quote, $config);
        $this->quote = $quote;
    }

    public function approve(InvoiceRepository $invoice_repo, QuoteRepository $quote_repo): ?Quote
    {
        if ($this->quote->status_id != Quote::STATUS_SENT) {
            return null;
        }

        $this->quote->setStatus(Quote::STATUS_APPROVED);
        $this->quote->date_approved = Carbon::now();
        $this->quote->save();

        if ($this->quote->customer->getSetting('should_convert_quote')) {
            (new ConvertQuoteToInvoice($this->quote, $invoice_repo))->execute();
        }

        event(new QuoteWasApproved($this->quote));

        // trigger
        $subject = trans('texts.quote_approved_subject');
        $body = trans('texts.quote_approved_body');
        $this->trigger($subject, $body, $quote_repo);

        return $this->quote;
    }

    /**
     * @param InvoiceRepository $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param array $data
     * @return Quote|null
     */
    public function reject(InvoiceRepository $invoice_repo, QuoteRepository $quote_repo, array $data = []): ?Quote
    {
        if ($this->quote->status_id != Quote::STATUS_SENT) {
            return null;
        }

        if (!empty($data['public_notes'])) {
            $this->quote->public_notes = $data['public_notes'];
        }

        $this->quote->setStatus(Quote::STATUS_REJECTED);
        $this->quote->date_rejected = Carbon::now();
        $this->quote->save();

        event(new QuoteWasRejected($this->quote));

        // trigger
        $subject = trans('texts.quote_rejected_subject');
        $body = trans('texts.quote_rejected_body');
        $this->sendEmail(null, $subject, $body);

        return $this->quote;
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return Quote|null
     */
    public function sendEmail($contact = null, $subject, $body, $template = 'quote'): ?Quote
    {
        if (!$this->sendInvitationEmails($subject, $body, $template, $contact)) {
            return null;
        }

        return $this->quote;
    }

    /**
     * @param InvoiceRepository $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param array $data
     * @return Quote|null
     */
    public function requestChange(
        InvoiceRepository $invoice_repo,
        QuoteRepository $quote_repo,
        array $data = []
    ): ?Quote {
        if ($this->quote->status_id != Quote::STATUS_SENT) {
            return null;
        }

        if (!empty($data['public_notes'])) {
            $this->quote->public_notes = $data['public_notes'];
        }

        $this->quote->setStatus(Quote::STATUS_CHANGE_REQUESTED);
        $this->quote->save();

        event(new QuoteChangeWasRequested($this->quote));

        // trigger
        $subject = trans('texts.quote_change_requested_subject');
        $body = trans('texts.quote_change_requested_body');
        $this->trigger($subject, $body, $quote_repo);

        return $this->quote;
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
            $contact = $this->quote->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new InvoicePdf($this->quote)), $this->quote, $contact, $update);
    }

    /**
     * @return Quote
     */
    public function calculateInvoiceTotals(): Quote
    {
        return $this->calculateTotals($this->quote);
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
     * @throws ReflectionException
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
        $recurringQuote = (new RecurringQuoteRepository(new RecurringQuote))->save(
            $arrRecurring,
            QuoteToRecurringQuoteFactory::create($this->quote)
        );

        $this->quote->recurring_quote_id = $recurringQuote->id;
        $this->quote->save();

        return $recurringQuote;
    }
}
