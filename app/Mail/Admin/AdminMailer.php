<?php


namespace App\Mail\Admin;

use App\Events\EmailFailedToSend;
use App\Models\Invitation;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use App\ViewModels\UserViewModel;
use Exception;
use Illuminate\Mail\Mailable;

class AdminMailer extends Mailable
{

    public $subject;

    public $entity;

    /**
     * @var array
     */
    protected array $button = [];

    /**
     * @var User
     */
    protected User $user;
    /**
     * @var string
     */
    protected string $message;
    /**
     * @var string
     */
    protected string $template;

    /**
     * @var Invitation|null
     */
    protected ?Invitation $invitation;

    /**
     * AdminMailer constructor.
     * @param string $template
     * @param $entity
     * @param Invitation|null $invitation
     */
    public function __construct(string $template, $entity, Invitation $invitation = null)
    {
        $this->template = $template;
        $this->entity = $entity;
        $this->invitation = $invitation;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function setSubject(array $data): bool
    {
        $this->subject = trans(
            'texts.notification_' . $this->template . '_subject',
            $data
        );

        return true;
    }

    protected function setMessage(array $data)
    {
        $this->message = trans(
            'texts.notification_' . $this->template,
            $data

        );

        return true;
    }

    /**
     * @param array $message_array
     * @return AdminMailer|bool
     */
    protected function execute()
    {
        $message_array = [
            'title'       => $this->subject,
            'body'        => $this->message,
            'signature'   => !empty($this->entity->account->settings->email_signature) ? $this->entity->account->settings->email_signature : '',
            'logo'        => (new AccountViewModel($this->entity->account))->logo(),
            'show_footer' => empty($this->entity->account->domains->plan) || !in_array(
                    $this->entity->account->domains->plan->code,
                    ['STDM', 'STDY']
                )
        ];

        if (!empty($this->button)) {
            $message_array = array_merge($this->button, $message_array);
        }

        $template = !in_array(
            get_class($this->entity),
            ['App\Models\Lead', 'App\Models\PurchaseOrder']
        ) ? $this->entity->customer->getSetting(
            'email_style'
        ) : $this->entity->account->settings->email_style;

        try {
            return $this->to($this->user->email)
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->replyTo($this->user->email, (new UserViewModel($this->user))->name())
                        ->subject($this->subject)
                        ->markdown(
                            empty($template) ? 'email.admin.new' : 'email.template.' . $template,
                            [
                                'data' => $message_array,
                            ]
                        )->withSwiftMessage( //https://stackoverflow.com/questions/42207987/get-message-id-with-laravel-mailable
                    function ($swiftmessage) {
                        $swiftmessage->entity = !empty($this->invitation) ? $this->invitation : $this->entity;
                    }
                );
        } catch (Exception $exception) {
            event(new EmailFailedToSend($this->entity, $exception->getMessage()));
            return false;
        }

        return true;
    }

    protected function getUrl()
    {
        $url = $this->entity->account->portal_domain;

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        $url = rtrim($url, '/') . '/portal/';

        return $url;
    }
}