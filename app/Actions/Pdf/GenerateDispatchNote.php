<?php

namespace App\Actions\Invoice;


use App\Models\Invoice;
use App\Components\Pdf\InvoicePdf;
use App\Jobs\Pdf\CreatePdf;

class GenerateDispatchNote
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function execute($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->entity->customer->primary_contact()->first();
        }

        $entity = get_class($this->entity) === 'App\\Models\\Order' ? CloneOrderToInvoiceFactory::create(
            $this->entity,
            $this->entity->user,
            $this->entity->account
        ) : $this->entity;

        return CreatePdf::dispatchNow(
            (new InvoicePdf($entity)),
            $this->entity,
            $contact,
            $update,
            'dispatch_note'
        );
    }
}
