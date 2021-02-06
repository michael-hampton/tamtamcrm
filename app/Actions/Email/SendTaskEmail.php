<?php

namespace App\Actions\Email;


use App\Events\Lead\LeadWasEmailed;
use App\Events\Task\TaskWasEmailed;
use App\Jobs\Email\SendEmail;
use App\Models\Lead;
use App\Models\Task;

class SendTaskEmail
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function execute($contact = null, $subject = '', $body = '', $template = 'deal')
    {
        $subject = !empty($subject) ? $subject : $this->task->account->getSetting('email_subject_task');
        $body = !empty($body) ? $body : $this->task->account->getSetting('email_template_task');

        SendEmail::dispatchNow($this->task, $subject, $body, 'task', $this->task->customer->contacts->first());

        event(new TaskWasEmailed($this->task));
    }

}