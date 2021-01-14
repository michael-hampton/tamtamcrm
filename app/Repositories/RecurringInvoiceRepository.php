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
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * RecurringInvoiceRepository
 */
class RecurringInvoiceRepository extends BaseRepository
{
    use BuildVariables;
    use CalculateRecurring;

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
    public function createInvoice(array $data, RecurringInvoice $recurring_invoice): ?RecurringInvoice
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
        $is_add = !empty($invoice->id);

        $invoice->fill($data);
        $invoice = $this->populateDefaults($invoice);
        $invoice = $this->formatNotes($invoice);
        $invoice = $invoice->service()->calculateInvoiceTotals();
        $invoice->setNumber();

        $invoice->save();

        $this->saveInvitations($invoice, $data);

        if (!$is_add) {
            event(new RecurringInvoiceWasUpdated($invoice));
        }

        return $invoice->fresh();
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
