<?php

namespace App\Services\Quote;


use App\Services\Email\DispatchEmail;
use App\Events\Quote\QuoteWasApproved;
use App\Models\Quote;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use Carbon\Carbon;

class Approve
{

    private Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function execute(InvoiceRepository $invoice_repo, QuoteRepository $quote_repo): ?Quote
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
        (new DispatchEmail($this->quote))->execute(null, $subject, $body);

        if (!empty($this->quote->customer->getSetting('should_archive_quote'))) {
            $this->quote->archive();
        }

        return $this->quote;
    }
}