<?php

namespace App\Http\Controllers;

use App\Components\Pdf\InvoicePdf;
use App\Components\Pdf\LeadPdf;
use App\Components\Pdf\PurchaseOrderPdf;
use App\Components\Pdf\TaskPdf;
use App\Traits\MakesInvoiceHtml;
use App\Utils\TemplateEngine;
use Illuminate\Http\Response;
use ReflectionException;


class TemplateController extends Controller
{
    use MakesInvoiceHtml;

    public function __construct()
    {
    }

    /**
     * Returns a template filled with entity variables
     *
     * @return Response
     * @throws ReflectionException
     */
    public function show()
    {
        // if no entity provided default to invoice
        $entity = request()->has('entity') ? request()->input('entity') : 'Invoice';
        $entity_id = request()->has('entity_id') ? request()->input('entity_id') : '';
        $subject = request()->has('subject') ? request()->input('subject') : '';
        $body = request()->has('body') ? request()->input('body') : '';
        $template = request()->has('template') ? request()->input('template') : '';
        $class = 'App\Models\\' . ucfirst($entity);

        $entity_object = !$entity_id ? $class::first() : $class::whereId($entity_id)->first();

        switch ($class) {
            case in_array($class, ['App\Models\Cases', 'App\Models\Task', 'App\Models\Deal']):
                $objPdfBuilder = new TaskPdf($entity_object);
                break;
            case 'App\Models\Lead':
                $objPdfBuilder = new LeadPdf($entity_object);
                break;
            case 'App\Models\PurchaseOrder':
                $objPdfBuilder = new PurchaseOrderPdf($entity_object);
                break;
            default:
                $objPdfBuilder = new InvoicePdf($entity_object);
        }

        $data = (new TemplateEngine(
            $objPdfBuilder, $body, $subject, $entity, $entity_id, $template
        ))->build();

        return response()->json($data, 200);
    }
}
