<?php


namespace App\Components\Pdf;


class PdfFactory
{

    public function create($entity)
    {
        switch (get_class($entity)) {
            case in_array(get_class($entity), ['App\Models\Task', 'App\Models\Deal']):
                return new TaskPdf($entity);
                break;
            case 'App\Models\Cases':
                return new TaskPdf($entity, 'case');
                break;
            case 'App\Models\RecurringInvoice':
                return new InvoicePdf($entity, 'invoice');
                break;
            case 'App\Models\RecurringQuote':
                return new InvoicePdf($entity, 'quote');
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
