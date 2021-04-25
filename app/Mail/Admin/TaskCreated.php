<?php

namespace App\Mail\Admin;

use App\Models\Task;
use App\Models\User;
use App\Traits\Money;
use App\ViewModels\AccountViewModel;
use App\ViewModels\CustomerViewModel;
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
        $this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'    => $this->formatCurrency($this->task->valued_at, $this->task->customer),
            'customer' => (new CustomerViewModel($this->task->customer))->name()
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => $this->getUrl() . 'tasks/' . $this->task->id,
            'button_text' => trans('texts.view_task')
        ];
    }
}
