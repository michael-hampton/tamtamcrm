<?php

namespace App\Jobs\Task;

use App\Factory\QuoteToRecurringQuoteFactory;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Repositories\RecurringQuoteRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class SaveTaskTimes
{
    use Dispatchable;

    private $request;
    private $account;
    private $quote;

    /**
     * Create a new job instance.
     *
     * @param Request $request
     * @param Account $account
     * @param Quote $quote
     */
    public function __construct(Request $request, Account $account, Quote $quote)
    {
        $this->request = $request;
        $this->account = $account;
        $this->quote = $quote;
    }

    /**
     *
     */
    public function handle(): ?RecurringQuote
    {
        if ($this->request->has('recurring') && !empty($this->request->recurring)) {
            $recurring = json_decode($this->request->recurring, true);
            $arrRecurring['start_date'] = $recurring['start_date'];
            $arrRecurring['end_date'] = $recurring['end_date'];
            $arrRecurring['frequency'] = $recurring['frequency'];
            $arrRecurring['recurring_due_date'] = $recurring['recurring_due_date'];
            $recurringQuote = (new RecurringQuoteRepository(new RecurringQuote))->save(
                $arrRecurring,
                QuoteToRecurringQuoteFactory::create($this->quote)
            );

            return $recurringQuote;
        }

        return null;
    }
}
