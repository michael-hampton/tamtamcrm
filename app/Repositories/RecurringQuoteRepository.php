<?php

namespace App\Repositories;

use App\Events\RecurringQuote\RecurringQuoteWasCreated;
use App\Events\RecurringQuote\RecurringQuoteWasUpdated;
use App\Models\Account;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Repositories\Base\BaseRepository;
use App\Requests\SearchRequest;
use App\Search\RecurringQuoteSearch;
use App\Traits\BuildVariables;
use App\Traits\CalculateRecurring;
use App\Traits\CalculateDates;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * RecurringQuoteRepository
 */
class RecurringQuoteRepository extends BaseRepository
{
    use BuildVariables;
    use CalculateDates;

    /**
     * RecurringQuoteRepository constructor.
     * @param RecurringQuote $quote
     */
    public function __construct(RecurringQuote $quote)
    {
        parent::__construct($quote);
        $this->model = $quote;
    }

    /**
     * @param array $data
     * @param RecurringQuote $recurring_quote
     * @return RecurringQuote|null
     */
    public function create(array $data, RecurringQuote $recurring_quote): ?RecurringQuote
    {
        $recurring_quote->date_to_send = $this->calculateDate(
            !empty($data['frequency']) ? $data['frequency'] : 'MONTHLY'
        );
        $recurring_quote = $this->save($data, $recurring_quote);

        if (!empty($data['quote_id']) && !empty($recurring_quote)) {
            $quote = Quote::where('id', '=', $data['quote_id'])->first();
            $quote->recurring_quote_id = $recurring_quote->id;
            $quote->save();
        }

        event(new RecurringQuoteWasCreated($recurring_quote));

        return $recurring_quote;
    }

    /**
     * @param array $data
     * @param RecurringQuote $quote
     * @return RecurringQuote|null
     */
    public function save(array $data, RecurringQuote $quote): ?RecurringQuote
    {
        $quote->fill($data);
        $quote = $this->calculateTotals($quote);
        $quote = $quote->convertCurrencies($quote, $quote->total, config('taskmanager.use_live_exchange_rates'));
        $quote = $this->populateDefaults($quote);
        $quote = $this->formatNotes($quote);
        $quote->setNumber();

        $quote->save();

        $this->saveInvitations($quote, $data);

        return $quote->fresh();
    }

    public function update(array $data, RecurringQuote $recurring_quote): ?RecurringQuote
    {
        $recurring_quote = $this->save($data, $recurring_quote);

        event(new RecurringQuoteWasUpdated($recurring_quote));

        return $recurring_quote;
    }

    /**
     * @param int $id
     * @return RecurringQuote
     */
    public function findQuoteById(int $id): RecurringQuote
    {
        return $this->findOneOrFail($id);
    }


    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new RecurringQuoteSearch($this))->filter($search_request, $account);
    }


    public function getModel()
    {
        return $this->model;
    }
}
