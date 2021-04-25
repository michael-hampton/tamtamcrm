<?php

namespace App\Mail\Admin;

use App\Models\Lead;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use App\ViewModels\LeadViewModel;
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
        $this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     * @throws PresenterException
     */
    private function getData(): array
    {
        return [
            'customer' => (new LeadViewModel($this->lead))->name()
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => config('taskmanager.web_url') . '/#/leads?id=' . $this->lead->id,
            'button_text' => trans('texts.view_deal'),
        ];
    }
}
