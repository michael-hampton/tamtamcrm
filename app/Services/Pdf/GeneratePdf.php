<?php


namespace App\Services\Pdf;


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
        return CreatePdf::dispatchNow(
            (new PdfFactory)->create($this->entity),
            $this->entity,
            $contact === null ? $this->getContact($this->entity) : $contact,
            $update
        );
    }

    private function getContact($entity)
    {
        switch (get_class($entity)) {
            case 'App\Models\Lead':
                return null;
                break;
            case 'App\Models\PurchaseOrder':
                return $entity->company->primary_contact()->first();
                break;
            default:
                return $entity->customer->primary_contact()->first();
        }
    }
}
