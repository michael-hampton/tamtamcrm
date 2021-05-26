<?php

namespace App\Http\Controllers;

use App\Components\Pdf\PdfFactory;
use App\Models\EmailTemplate;
use App\Repositories\EmailTemplateRepository;
use App\Traits\BuildVariables;
use App\ViewModels\AccountViewModel;
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
        $template = (new EmailTemplateRepository(new EmailTemplate()))->getTemplateForType(request()->input('template'));

        // if no entity provided default to invoice
        $entity = request()->has('entity') ? request()->input('entity') : 'Invoice';
        $entity_id = request()->has('entity_id') ? request()->input('entity_id') : '';
        $subject = request()->has('subject') ? request()->input('subject') : '';
        $body = request()->has('body') ? request()->input('body') : '';
        $class = 'App\Models\\' . ucfirst($entity);

        $entity_object = !$entity_id ? $class::first() : $class::whereId($entity_id)->first();

        $objPdfBuilder = (new PdfFactory())->create($entity_object);

        $data = $this->build($template, $objPdfBuilder, $subject, $body);

//        $data = (new TemplateEngine(
//            $objPdfBuilder, $body, $subject, $entity, $entity_id, $template
//        ))->build();

        return response()->json($data, 200);
    }

    private function build(EmailTemplate $email_template, $objPdf, $subject, $body)
    {
        $entity_obj = $objPdf->getEntity();

        $subject = strlen($subject) > 0 ? $subject : $email_template->subject;
        $body = strlen($body) > 0 ? $body : $email_template->message;

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
        $viewModel = new AccountViewModel($entity_obj->account);
        $email_style = $entity_obj->account->settings->email_style;
        $wrapper = view('email.template.' . $email_style,
            [
                'data' => [
                    'title' => $subject,
                    'body' => $body,
                    'url' => config('taskmanager.web_url') . '/#/expenses?id=' . $entity_obj->id,
                    'button_text' => trans('texts.view_expense'),
                    'signature' => $entity_obj->account->settings->email_signature ?: '',
                    'logo' => $viewModel->logo(),
                    'show_footer' => empty($entity_obj->account->domains->plan) || !in_array($entity_obj->account->domains->plan->code, [
                            'PROM',
                            'PROY'
                        ])
                ]
            ])->render();

        return [
            'subject' => $subject,
            'body'    => $body,
            'wrapper' => $wrapper
        ];
    }
}
