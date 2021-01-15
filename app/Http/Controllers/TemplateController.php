<?php

namespace App\Http\Controllers;

use App\Components\Pdf\InvoicePdf;
use App\Components\Pdf\LeadPdf;
use App\Components\Pdf\PurchaseOrderPdf;
use App\Components\Pdf\TaskPdf;
use App\Traits\BuildVariables;
use App\Utils\TemplateEngine;
use Illuminate\Http\Response;
use League\CommonMark\CommonMarkConverter;
use ReflectionException;


class TemplateController extends Controller
{
    use BuildVariables;

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

        $data = $this->build($objPdfBuilder, $template, $subject, $body);

//        $data = (new TemplateEngine(
//            $objPdfBuilder, $body, $subject, $entity, $entity_id, $template
//        ))->build();

        return response()->json($data, 200);
    }

    private function build($objPdf, $template, $subject, $body)
    {
        $entity_obj = $objPdf->getEntity();

        $subject_template = str_replace("template", "subject", $template);
        $subject = strlen($subject) > 0 ? $subject : $entity_obj->account->settings->{$subject_template};
        $body = strlen($body) > 0 ? $body : $entity_obj->account->settings->{$template};

        $subject = $this->parseVariables($subject, $entity_obj);
        $body = $this->parseVariables($body, $entity_obj);

        $converter = new CommonMarkConverter(
            [
                'allow_unsafe_links' => false,
            ]
        );

        $body = $converter->convertToHtml($body);

        return $this->render($subject, $body, $entity_obj);
    }

    private function render($subject, $body, $entity_obj)
    {
        $email_style = $entity_obj->account->settings->email_style;
        $wrapper = view('email.template.' . $email_style, ['body' => $body])->render();

        return [
            'subject' => $subject,
            'body'    => $body,
            'wrapper' => $wrapper
        ];
    }
}
