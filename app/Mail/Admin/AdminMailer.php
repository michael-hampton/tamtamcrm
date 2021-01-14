<?php


namespace App\Mail\Admin;

use App\Events\EmailFailedToSend;
use App\Models\User;
use Exception;
use Illuminate\Mail\Mailable;

class AdminMailer extends Mailable
{

    public $subject;
    /**
     * @var User
     */
    protected User $user;

    /**
     * @var string
     */
    protected string $message;

    protected $entity;

    /**
     * @var string
     */
    protected string $template;

    /**
     * AdminMailer constructor.
     * @param string $template
     * @param $entity
     */
    public function __construct(string $template, $entity)
    {
        $this->template = $template;
        $this->entity = $entity;
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
    protected function execute(array $message_array)
    {
        $template = !in_array(
            get_class($this->entity),
            ['App\Models\Lead', 'App\Models\PurchaseOrder']
        ) ? $this->entity->customer->getSetting(
            'email_style'
        ) : $this->entity->account->settings->email_style;

        try {
            return $this->to($this->user->email)
                        ->from(config('taskmanager.from_email'))
                        ->subject($this->subject)
                        ->markdown(
                            empty($template) ? 'email.admin.new' : 'email.template.' . $template,
                            [
                                'data' => $message_array,
                            ]
                        );
        } catch (Exception $exception) {
            event(new EmailFailedToSend($this->entity, $exception->getMessage()));
            return false;
        }

        return true;
    }

    protected function getUrl()
    {
        $url = $this->entity->account->subdomain;

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        $url = rtrim($url, '/') . '/portal/';

        return $url;
    }
}