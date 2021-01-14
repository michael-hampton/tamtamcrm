<?php

namespace App\Repositories;

use App\Events\Quote\QuoteWasCreated;
use App\Events\Quote\QuoteWasUpdated;
use App\Jobs\Order\QuoteOrders;
use App\Jobs\Product\UpdateProductPrices;
use App\Models\Account;
use App\Models\Quote;
use App\Models\Task;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\QuoteRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\QuoteSearch;
use App\Traits\BuildVariables;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class QuoteRepository
 * @package App\Repositories
 */
class QuoteRepository extends BaseRepository implements QuoteRepositoryInterface
{
    use BuildVariables;

    /**
     * QuoteRepository constructor.
     *
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        parent::__construct($quote);
        $this->model = $quote;
    }

    /**
     * @param int $id
     * @return Quote
     */
    public function findQuoteById(int $id): Quote
    {
        return $this->findOneOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param Quote $quote
     * @return Quote
     */
    public function createQuote(array $data, Quote $quote): ?Quote
    {
        $quote = $this->save($data, $quote);

        if (!empty($data['recurring'])) {
            $recurring = json_decode($data['recurring'], true);
            $quote->service()->createRecurringQuote($recurring);
        }

        QuoteOrders::dispatchNow($quote);
        event(new QuoteWasCreated($quote));

        return $quote;
    }

    /**
     * @param array $data
     * @param Quote $quote
     * @return Quote|null
     */
    public function save(array $data, Quote $quote): ?Quote
    {
        $quote->fill($data);
        $quote = $this->populateDefaults($quote);
        $quote = $this->formatNotes($quote);
        $quote = $quote->service()->calculateInvoiceTotals();
        $quote->setNumber();

        $quote->save();

        $this->saveInvitations($quote, $data);

        //if ($quote->customer->getSetting('should_update_products') === true) {
        UpdateProductPrices::dispatchNow($quote->line_items);
        //}

        return $quote->fresh();
    }

    /**
     * @param array $data
     * @param Quote $quote
     * @return Quote|null
     */
    public function updateQuote(array $data, Quote $quote): ?Quote
    {
        $quote = $this->save($data, $quote);
        QuoteOrders::dispatchNow($quote);
        event(new QuoteWasUpdated($quote));

        return $quote;
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new QuoteSearch($this))->filter($search_request, $account);
    }

    /**
     * @param Task $objTask
     * @return Quote
     */
    public function getQuoteForTask(Task $objTask): Quote
    {
        return $this->model->where('task_id', $objTask->id)->first();
    }

    public function getExpiredQuotes()
    {
        return Quote::whereDate('due_date', '<', Carbon::today()->subDay()->toDateString())
                    ->where('is_deleted', '=', false)
                    ->whereIn(
                        'status_id',
                        [Quote::STATUS_SENT]
                    )->get();
    }

}
