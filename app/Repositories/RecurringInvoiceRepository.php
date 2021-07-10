<?php

namespace App\Repositories;

use App\Events\RecurringInvoice\RecurringInvoiceWasCreated;
use App\Events\RecurringInvoice\RecurringInvoiceWasUpdated;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Repositories\Base\BaseRepository;
use App\Requests\SearchRequest;
use App\Search\RecurringInvoiceSearch;
use App\Traits\BuildVariables;
use App\Traits\CalculateRecurring;
use App\Traits\CalculateDates;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * RecurringInvoiceRepository
 */
class RecurringInvoiceRepository extends BaseRepository
{
    use BuildVariables;
    use CalculateDates;

    /**
     * RecurringInvoiceRepository constructor.
     * @param RecurringInvoice $invoice
     */
    public function __construct(RecurringInvoice $invoice)
    {
        parent::__construct($invoice);
        $this->model = $invoice;
    }

    /**
     * @param array $data
     * @param RecurringInvoice $recurring_invoice
     * @return RecurringInvoice|null
     */
    public function create(array $data, RecurringInvoice $recurring_invoice): ?RecurringInvoice
    {
        $recurring_invoice->date_to_send = $this->calculateDate($data['frequency']);
        $recurring_invoice = $this->save($data, $recurring_invoice);

        if (!empty($data['invoice_id']) && !empty($recurring_invoice)) {
            $invoice = Invoice::where('id', '=', $data['invoice_id'])->first();
            $invoice->recurring_invoice_id = $recurring_invoice->id;
            $invoice->save();
        }

        event(new RecurringInvoiceWasCreated($recurring_invoice));

        return $recurring_invoice;
    }

    /**
     * @param array $data
     * @param RecurringInvoice $invoice
     * @return RecurringInvoice|null
     */
    public function save(array $data, RecurringInvoice $invoice): ?RecurringInvoice
    {
        $invoice->fill($data);
        $invoice = $this->calculateTotals($invoice);
        $invoice = $invoice->convertCurrencies($invoice, $invoice->total, config('taskmanager.use_live_exchange_rates'));
        $invoice = $this->populateDefaults($invoice);
        $invoice = $this->formatNotes($invoice);
        $invoice->setNumber();

        $invoice->save();

        $this->saveInvitations($invoice, $data);

        return $invoice->fresh();
    }

    /**
     * @param array $data
     * @param RecurringInvoice $recurring_invoice
     */
    public function update(array $data, RecurringInvoice $recurring_invoice)
    {
        $recurring_invoice = $this->save($data, $recurring_invoice);
        event(new RecurringInvoiceWasUpdated($recurring_invoice));

        return $recurring_invoice;
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new RecurringInvoiceSearch($this))->filter($search_request, $account);
    }

    /**
     * @param int $id
     * @return RecurringInvoice
     */
    public function findInvoiceById(int $id): RecurringInvoice
    {
        return $this->findOneOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }
}
