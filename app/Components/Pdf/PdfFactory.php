<?php


namespace App\Components\Pdf;


class PdfFactory
{

    public function create($entity)
    {
        switch (get_class($entity)) {
            case in_array(get_class($entity), ['App\Models\Cases', 'App\Models\Task', 'App\Models\Deal']):
                return new TaskPdf($entity);
                break;
            case 'App\Models\Lead':
                return new LeadPdf($entity);
                break;
            case 'App\Models\PurchaseOrder':
                return new PurchaseOrderPdf($entity);
                break;
            default:
                return new InvoicePdf($entity);
        }
    }
}