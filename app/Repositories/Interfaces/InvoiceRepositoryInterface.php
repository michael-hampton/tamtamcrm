<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Task;
use App\Requests\SearchRequest;
use Illuminate\Support\Collection;

interface InvoiceRepositoryInterface
{
    /**
     *
     * @param SearchRequest $search_request
     * @param Account $account
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param int $id
     * @return Invoice
     */
    public function findInvoiceById(int $id): Invoice;

    /**
     *
     * @param Task $objTask
     * @return Invoice
     */
    public function getInvoiceForTask(Task $objTask): Invoice;

    /**
     * @param int $status
     * @return Collection
     */
    public function findInvoicesByStatus(int $status): Collection;

    /**
     * @param array $data
     * @param Invoice $invoice
     * @return Invoice|null
     */
    public function save(array $data, Invoice $invoice): ?Invoice;

    /**
     * @param array $data
     * @param Invoice $invoice
     * @return Invoice|null
     */
    public function createInvoice(array $data, Invoice $invoice): ?Invoice;

    /**
     * @param array $data
     * @param Invoice $invoice
     * @return Invoice|null
     */
    public function updateInvoice(array $data, Invoice $invoice): ?Invoice;

}
