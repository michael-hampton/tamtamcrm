<?php

namespace App\Actions\Email;


use App\Events\Lead\LeadWasEmailed;
use App\Jobs\Email\SendEmail;
use App\Models\Lead;

class SendLeadEmail
{
    private Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function execute($subject = '', $body = '', $template = 'lead')
    {
        $subject = !empty($subject) ? $subject : $this->lead->account->getSetting('email_subject_lead');
        $body = !empty($body) ? $body : $this->lead->account->getSetting('email_template_lead');

        SendEmail::dispatchNow($this->lead, $subject, $body, 'lead', $this->lead);

        event(new LeadWasEmailed($this->lead));
    }
}