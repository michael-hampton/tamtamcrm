<?php

namespace App\Mail\Admin;

use App\Models\Cases;
use App\Models\User;
use App\ViewModels\AccountViewModel;
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
        $this->buildButton();
        $this->execute();

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
    private function buildButton(): void
    {
        $this->button = [
            'url'         => $this->getUrl() . 'cases/' . $this->case->id,
            'button_text' => trans('texts.view_case'),
        ];
    }
}
