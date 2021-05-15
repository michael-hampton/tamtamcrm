<?php

namespace App\Services\Quote;


use App\Factory\QuoteToRecurringQuoteFactory;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Repositories\RecurringQuoteRepository;

class GenerateRecurringQuote
{

    private Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @param array $recurring
     * @return RecurringQuote|null
     */
    public function execute(array $recurring): ?RecurringQuote
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