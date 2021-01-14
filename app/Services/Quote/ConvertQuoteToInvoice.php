<?php

namespace App\Services\Quote;

use App\Factory\CloneQuoteToInvoiceFactory;
use App\Models\Invoice;
use App\Models\Quote;
use App\Repositories\InvoiceRepository;
use ReflectionException;

/**
 * Class ConvertQuote
 * @package App\Services\Quote
 */
class ConvertQuoteToInvoice
{
    /**
     * @var Quote
     */
    private Quote $quote;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * ConvertQuote constructor.
     * @param Quote $quote
     * @param InvoiceRepository $invoice_repo
     */
    public function __construct(Quote $quote, InvoiceRepository $invoice_repo)
    {
        $this->quote = $quote;
        $this->invoice_repo = $invoice_repo;
    }

    /**
     * @return Invoice|null
     * @throws ReflectionException
     */
    public function execute(): ?Invoice
    {
        if (!empty($this->quote->invoice_id) || $this->quote->status_id === Quote::STATUS_EXPIRED) {
            return null;
        }

        $invoice = CloneQuoteToInvoiceFactory::create(
            $this->quote,
            $this->quote->user,
            $this->quote->account
        );

        $invoice = $this->invoice_repo->save(
            [
                'status_id' => Invoice::STATUS_SENT
            ],
            $invoice
        );

        $this->invoice_repo->markSent($invoice);

        $this->quote->setInvoiceId($invoice->id);
        $this->quote->setStatus(Quote::STATUS_CONVERTED);
        $this->quote->save();

        return $invoice;
    }
}
