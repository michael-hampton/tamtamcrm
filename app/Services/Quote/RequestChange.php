<?php

namespace App\Services\Quote;


use App\Events\Quote\QuoteChangeWasRequested;
use App\Models\Quote;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;

class RequestChange
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
    public function execute(
        InvoiceRepository $invoice_repo,
        QuoteRepository $quote_repo,
        array $data = []
    ): ?Quote {
        if ($this->quote->status_id != Quote::STATUS_SENT) {
            return null;
        }

        if (!empty($data['customer_note'])) {
            $this->quote->customer_note = $data['customer_note'];
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
}