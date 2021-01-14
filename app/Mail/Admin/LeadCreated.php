<?php

namespace App\Mail\Admin;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Laracasts\Presenter\Exceptions\PresenterException;

class LeadCreated extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var Lead
     */
    private Lead $lead;


    /**
     * LeadCreated constructor.
     * @param Lead $lead
     * @param User $user
     */
    public function __construct(Lead $lead, User $user)
    {
        parent::__construct('lead_created', $lead);

        $this->lead = $lead;
        $this->entity = $lead;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return void
     * @throws PresenterException
     */
    public function build()
    {
        $data = $this->getData();
        $this->setSubject($data);
        $this->setMessage($data);
        $this->execute($this->buildMessage());
    }

    /**
     * @return array
     * @throws PresenterException
     */
    private function getData(): array
    {
        return [
            'customer' => $this->lead->present()->name()
        ];
    }

    /**
     * @return array
     */
    private function buildMessage(): array
    {
        return [
            'title'       => $this->subject,
            'body'        => $this->message,
            'url'         => config('taskmanager.site_url') . '/portal/leads/' . $this->lead->id,
            'button_text' => trans('texts.view_deal'),
            'signature'   => isset($this->lead->account->settings->email_signature) ? $this->lead->account->settings->email_signature : '',
            'logo'        => $this->lead->account->present()->logo(),
        ];
    }
}
