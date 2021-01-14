<?php

namespace App\Factory;

use App\Models\Quote;
use App\Models\RecurringQuote;

/**
 * Class QuoteToRecurringQuoteFactory
 * @package App\Factory
 */
class QuoteToRecurringQuoteFactory
{
    /**
     * @param Quote $quote
     * @return RecurringQuote
     */
    public static function create(Quote $quote): RecurringQuote
    {
        $recurring_invoice = new RecurringQuote;
        $recurring_invoice->fill($quote->toArray());
        $recurring_invoice->number = null;
        $recurring_invoice->status_id = RecurringQuote::STATUS_DRAFT;
        $recurring_invoice->number = '';
        $recurring_invoice->date = date_create()->format('Y-m-d');
        $recurring_invoice->customer_id = $quote->customer_id;
        $recurring_invoice->balance = $quote->total;
        $recurring_invoice->user_id = $quote->user_id;
        $recurring_invoice->account_id = $quote->account_id;
        $recurring_invoice->frequency = 'MONTHLY';

        return $recurring_invoice;
    }
}
