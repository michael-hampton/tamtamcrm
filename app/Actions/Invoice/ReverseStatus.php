<?php

namespace App\Actions\Invoice;


use App\Models\Invoice;

class ReverseStatus
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function execute(): ?Invoice
    {
        if (!in_array($this->invoice->status_id, [Invoice::STATUS_CANCELLED, Invoice::STATUS_REVERSED])) {
            return null;
        }

        $this->invoice->date_cancelled = null;
        $this->invoice->rewindCache();

        return $this->invoice->fresh();
    }
}