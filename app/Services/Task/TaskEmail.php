<?php

namespace App\Services\Task;

use App\Events\Task\TaskWasEmailed;
use App\Jobs\Email\SendEmail;
use App\Models\task;
use App\Traits\MakesInvoiceHtml;

class TaskEmail
{
    use MakesInvoiceHtml;

    /**
     * @var task
     */
    private Task $task;

    /**
     * @var string
     */
    private $subject = '';

    /**
     * @var string
     */
    private $body = '';

    /**
     * TaskEmail constructor.
     * @param task $task
     * @param string $subject
     * @param string $body
     */
    public function __construct(Task $task, $subject = '', $body = '')
    {
        $this->task = $task;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Builds the correct template to send
     * @return void
     */
    public function execute()
    {
        $subject = strlen($this->subject) > 0 ? $this->subject : $this->task->account->getSetting('email_subject_task');
        $body = strlen($this->body) > 0 ? $this->body : $this->task->account->getSetting('email_template_task');

        SendEmail::dispatchNow($this->task, $subject, $body, 'task', $this->task->customer->contacts->first());

        event(new TaskWasEmailed($this->task));
    }
}
