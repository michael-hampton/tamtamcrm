<?php


namespace App\Actions\Pdf;


use App\Components\Pdf\PdfFactory;
use App\Jobs\Pdf\CreatePdf;

class GeneratePdf
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

        $label = '';

        return CreatePdf::dispatchNow(
            (new PdfFactory)->create($this->entity),
            $this->entity,
            $contact,
            $update,
            $label
        );
    }
}
