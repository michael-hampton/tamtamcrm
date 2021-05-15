<?php

namespace App\Services\Quote;


use App\Events\Quote\QuoteWasRejected;
use App\Models\Quote;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use Carbon\Carbon;

class Reject
{

    private Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @param InvoiceRepository $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param array $data
     * @return Quote|null
     */
    public function execute(InvoiceRepository $invoice_repo, QuoteRepository $quote_repo, array $data = []): ?Quote
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
}