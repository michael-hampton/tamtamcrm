<?php

namespace App\Repositories;

use App\Actions\Invoice\GenerateRecurringInvoice;
use App\Events\Invoice\InvoiceWasCreated;
use App\Events\Invoice\InvoiceWasUpdated;
use App\Jobs\Order\InvoiceOrders;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Task;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use App\Traits\BuildVariables;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{

    use BuildVariables;

    /**
     * InvoiceRepository constructor.
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
        $this->model = $invoice;
    }

    /**
     * @param int $id
     *
     * @return Invoice
     * @throws Exception
     */
    public function findInvoiceById(int $id): Invoice
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return InvoiceSearch|LengthAwarePaginator
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new InvoiceSearch($this))->filter($search_request, $account);
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Task $objTask
     * @return Invoice
     */
    public function getInvoiceForTask(Task $objTask): Invoice
    {
        return $this->model->where('task_id', '=', $objTask->id)->first();
    }

    public function findInvoicesByStatus(int $status): Collection
    {
        return $this->model->where('status_id', '=', $status)->get();
    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @return Quote|null
     */
    public function update(array $data, Invoice $invoice): ?Invoice
    {
        $invoice = $this->save($data, $invoice);
        InvoiceOrders::dispatchNow($invoice);
        event(new InvoiceWasUpdated($invoice));

        return $invoice;
    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @return Invoice|null
     */
    public function save(array $data, Invoice $invoice): ?Invoice
    {
        $original_amount = $invoice->total * 1;
        $invoice->fill($data);
        $invoice = $this->calculateTotals($invoice);
        $invoice = $invoice->convertCurrencies($invoice, $invoice->total,
            config('taskmanager.use_live_exchange_rates'));
        $invoice = $this->populateDefaults($invoice);
        $invoice = $this->formatNotes($invoice);
        $invoice->setNumber();

        $invoice->save();

        $this->saveInvitations($invoice, $data);

        $invoice_total = $invoice->total * 1;

        if ($invoice->status_id !== Invoice::STATUS_DRAFT && $original_amount !== $invoice_total) {
            $updated_amount = $invoice->total - $original_amount;
            $invoice->updateCustomerBalance($updated_amount);
        }

        return $invoice->fresh();
    }

    /**
     * @param array $data
     * @param Invoice $invoice
     * @return Invoice|null
     * @return Invoice|null
     */
    public function create(array $data, Invoice $invoice): ?Invoice
    {
        $invoice = $this->save($data, $invoice);

        InvoiceOrders::dispatchNow($invoice);

        if (!empty($data['recurring'])) {
            $recurring = json_decode($data['recurring'], true);
            (new GenerateRecurringInvoice($invoice))->execute($recurring);
        }

        event(new InvoiceWasCreated($invoice));

        return $invoice;
    }

    public function getInvoicesForAutoBilling()
    {
        return Invoice::where('hide', 0)
            ->whereNull('deleted_at')
            ->whereNull('is_recurring')
            ->whereNotNull('recurring_invoice_id')
            ->where('balance', '>', 0)
            ->where('due_date', Carbon::today())
            ->get();
    }

    /**
     * @return mixed
     */
    public function getInvoiceReminders()
    {
        return Invoice::whereDate('date_to_send', '=', Carbon::today()->toDateString())
            ->where('hide', '=', false)
            ->where('balance', '>', 0)
            ->whereIn(
                'status_id',
                [Invoice::STATUS_DRAFT, Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL]
            )->get();
    }

    public function getExpiredInvoices()
    {
        return Invoice::whereDate('due_date', '<', Carbon::today()->subDay()->toDateString())
            ->where('hide', '=', false)
            ->where('balance', '>', 0)
            ->whereIn(
                'status_id',
                [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL]
            )->get();
    }

    public function scopeOutstandingInvoices()
    {
        return Invoice::where('hide', false)
            ->whereIn('status_id', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])
            ->where('balance', '>', 0);

    }
}
