<?php

namespace App\Mail\Admin;

use App\Models\Cases;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class CaseOverdue extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var Cases
     */
    private Cases $case;

    /**
     * CaseOverdue constructor.
     * @param Cases $case
     * @param User $user
     */
    public function __construct(Cases $case, User $user)
    {
        parent::__construct('case_overdue', $case);

        $this->case = $case;
        $this->entity = $case;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return bool
     */
    public function build()
    {
        $data = $this->getData();

        $this->setSubject($data);
        $this->setMessage($data);
        $this->execute($this->buildMessage());

        return true;
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'customer' => $this->case->customer->name,
            'number'   => $this->case->number,
            'due_date' => $this->case->due_date
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
            'url'         => $this->getUrl() . 'cases/' . $this->case->id,
            'button_text' => trans('texts.view_case'),
            'signature'   => !empty($this->settings) ? $this->settings->email_signature : '',
            'logo'        => $this->case->account->present()->logo(),
        ];
    }
}
