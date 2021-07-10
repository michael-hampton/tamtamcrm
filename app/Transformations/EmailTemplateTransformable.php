<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\File;
use App\Models\Invitation;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Models\RecurringQuoteInvitation;
use App\Models\Reminders;

trait EmailTemplateTransformable
{

    /**
     * @param RecurringQuote $quote
     * @return array
     */
    protected function transformTemplate(EmailTemplate $email_template)
    {
        return [
            'id'                => (int)$email_template->id,
            'account_id'        => (int)$email_template->account_id,
            'user_id'           => (int)$email_template->user_id,
            'enabled'           => (bool)$email_template->enabled,
            'subject'           => (string)$email_template->subject,
            'message'           => (string)$email_template->message,
            'template'          => (string)$email_template->template,
            'amount_to_charge'  => $email_template->amount_to_charge,
            'frequency_id'      => (int)$email_template->frequency_id,
            'percent_to_charge' => $email_template->percent_to_charge
        ];
    }

}
