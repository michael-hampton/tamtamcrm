<?php

namespace App\Jobs\Email;

use App\Actions\Pdf\GeneratePdf;
use App\Components\Pdf\PdfFactory;
use App\Events\EmailFailedToSend;
use App\Factory\EmailFactory;
use App\Factory\ErrorLogFactory;
use App\Jobs\Invoice\CreateUbl;
use App\Mail\SendMail;
use App\Models\Email;
use App\Models\ErrorLog;
use App\Models\Invoice;
use App\Repositories\EmailRepository;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerContactViewModel;
use App\ViewModels\LeadViewModel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use ReflectionClass;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $entity;

    private $subject;

    private $contact;

    private $body;

    private $designer;

    private $footer;

    private $template;

    /**
     * Create a new job instance.
     *
     * @param $entity
     * @param $subject
     * @param $body
     * @param $template
     * @param null $contact
     * @param array $footer
     */
    public function __construct($entity, $subject, $body, $template, $contact = null, array $footer = [])
    {
        $this->entity = $entity;
        $this->contact = $contact;
        $this->subject = $subject;
        $this->body = $body;
        $this->footer = $footer;
        $this->template = $template;
    }

    public function handle()
    {
        $settings = $this->entity->account->settings;

        $objPdf = (new PdfFactory())->create($this->entity);

        $objPdf->build();
        $labels = $objPdf->getLabels();
        $values = $objPdf->getValues();

        $this->subject = $objPdf->parseLabels($labels, $this->subject);
        $body = $objPdf->parseValues($values, $this->body);
        $design_style = $settings->email_style;

        if ($design_style == 'custom') {
            $email_style_custom = $settings->email_style_custom;
            $body = str_replace("$body", $body, $email_style_custom);
        }

        $message = (new SendMail($this->entity, $this->contact))
            ->setData($this->buildMailMessageData($settings, $body, $design_style))
            ->setBody($body)
            ->setFooter($this->footer)
            ->setDesign($design_style)
            ->setTemplate($this->template)
            ->setSubject($this->subject);


        if (strlen($settings->reply_to_email) > 0) {
            $reply_to_name = !empty($settings->reply_to_name) ? $settings->reply_to_name
                : (new AccountViewModel($this->entity->account))->name();
            $message->setReplyTo($settings->reply_to_email, $reply_to_name);
        }

        if (strlen($settings->bcc_email) > 0) {
            $bcc = explode(',', $settings->bcc_email);
            $message->setBcc($bcc);
        }

        if ($settings->pdf_email_attachment && (new ReflectionClass($this->entity))->getShortName() !== 'Payment') {
            $message->setAttachments(public_path((new GeneratePdf($this->entity))->execute($this->contact)));
        }

        foreach ($this->entity->files as $file) {
            $message->setAttachments($file->generateUrl(), ['as' => $file->name]);
        }

        if ($this->entity instanceof Invoice && $settings->ubl_email_attachment) {
            $ubl_string = CreateUbl::dispatchNow($this->entity);
            $file_name = $this->entity->number . '.xml';
            $message->setAttachmentData($ubl_string, $file_name);
        }

        try {
            Mail::to($this->contact->email, (new CustomerContactViewModel($this->contact))->name())
                ->send($message);
        } catch (Exception $e) {
            echo $e->getMessage();
            die('here99');
            event(new EmailFailedToSend($this->entity, $e->getMessage()));
        }

        die('here');

        $sent_successfully = count(Mail::failures()) === 0;

        if (!$sent_successfully) {
            $this->createLogEntry(Mail::failures());
        }

        $this->toDatabase($this->subject, $body, $sent_successfully);

        return $message;
    }

    private function buildMailMessageData($settings, $body, $design): array
    {
        $viewModel = new AccountViewModel($this->entity->account);

        return [
            'design' => $design,
            'footer' => $this->footer,
            'url' => !empty($this->footer) ? $this->footer['link'] : '',
            'button_text' => !empty($this->footer) ? $this->footer['text'] : '',
            'title' => $this->subject,
            'body' => $body,
            'signature' => !empty($this->entity->account->settings->email_signature) ? $this->entity->account->settings->email_signature : '',
            'logo' => (new AccountViewModel($this->entity->account))->logo(),
            'show_footer' => empty($this->entity->account->domains->plan) || !in_array(
                    $this->entity->account->domains->plan->code,
                    ['STDM', 'STDY']
                )
        ];
    }

    private function createLogEntry($errors)
    {
        $user = $this->entity->user;
        $error_log = ErrorLogFactory::create($this->entity->account, $user, $this->entity->customer);
        $error_log->data = $errors;
        $error_log->error_type = ErrorLog::EMAIL;
        $error_log->error_result = ErrorLog::FAILURE;
        $error_log->entity = get_class($this->entity);

        $error_log->save();
    }

    /**
     * @param $subject
     * @param $body
     * @param $sent_successfully
     * @return bool
     */
    private function toDatabase($subject, $body, $sent_successfully)
    {
        $user = auth()->user();

        if (empty($user)) {
            $user = $this->entity->user;
        }

        $entity = get_class($this->entity);

        if ($entity === 'App\Models\Lead') {
            $contact = $this->entity;
            $contactViewModel = new LeadViewModel($this->entity);
        } else {
            $contact = $this->contact;
            $contactViewModel = new CustomerContactViewModel($contact);
        }

        // check if already sent
        $email = Email::whereSubject($subject)
            ->whereEntity($entity)
            ->whereEntityId($this->entity->id)
            ->whereRecipientEmail($contact->email)
            ->whereFailedToSend(1)
            ->first();


        if (!empty($email) && !$sent_successfully) {
            $email->increment('number_of_tries', 1, ['failed_to_send' => 1]);
            return false;
        }

        $email = EmailFactory::create($user->id, $this->entity->account_id);

        (new EmailRepository(new Email))->save(
            [
                'subject' => $subject,
                'body' => $body,
                'entity' => $entity,
                'entity_id' => $this->entity->id,
                'recipient' => $contactViewModel->name(),
                'recipient_email' => $contact->email,
                'template' => $this->template,
                'sent_at' => Carbon::now(),
                'failed_to_send' => $sent_successfully === false,
            ],
            $email
        );
    }
}
