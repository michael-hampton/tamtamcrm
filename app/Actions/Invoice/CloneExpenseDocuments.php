<?php

namespace App\Actions\Invoice;


use App\Models\Invoice;

class CloneExpenseDocuments
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @param Invoice $invoice
     * @param $expenses
     * @return bool
     */
    public function clone($expenses): bool
    {
        foreach ($expenses as $expense) {
            foreach ($expense->files as $file) {
                $clone = $file->replicate();

                $this->invoice->files()->save($clone);
            }
        }

        return true;
    }
}