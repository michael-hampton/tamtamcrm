<?php

namespace App\Mail\Admin;

use App\Models\Task;
use App\Models\User;
use App\Traits\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class TaskCreated extends AdminMailer
{
    use Queueable, SerializesModels, Money;

    private Task $task;

    /**
     * TaskCreated constructor.
     * @param Task $task
     * @param User $user
     */
    public function __construct(Task $task, User $user)
    {
        parent::__construct('task_created', $task);

        $this->task = $task;
        $this->entity = $task;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return void
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
     */
    private function getData(): array
    {
        return [
            'total'    => $this->formatCurrency($this->task->valued_at, $this->task->customer),
            'customer' => $this->task->customer->present()->name()
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
            'url'         => config('taskmanager.site_url') . 'portal/payments/' . $this->task->id,
            'button_text' => trans('texts.view_deal'),
            'signature'   => !empty($this->settings) ? $this->settings->email_signature : '',
            'logo'        => $this->task->account->present()->logo(),
        ];
    }
}
