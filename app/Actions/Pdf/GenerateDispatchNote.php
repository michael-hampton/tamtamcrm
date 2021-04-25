<?php

namespace App\Actions\Pdf;


use App\Components\Pdf\InvoicePdf;
use App\Factory\CloneOrderToInvoiceFactory;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Invoice;

class GenerateDispatchNote
{
    private $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
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
