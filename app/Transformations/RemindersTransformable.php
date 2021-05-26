<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;
use App\Models\Quote;
use App\Models\RecurringQuote;
use App\Models\RecurringQuoteInvitation;
use App\Models\Reminders;

trait RemindersTransformable
{

    /**
     * @param RecurringQuote $quote
     * @return array
     */
    protected function transformReminders(Reminders $reminder)
    {
        return [
            'id'                   => (int)$reminder->id,
            'account_id'           => (int)$reminder->account_id,
            'user_id'              => (int)$reminder->user_id,
            'enabled'              => (bool)$reminder->enabled,
            'number_of_days_after' => (int)$reminder->number_of_days_after,
            'scheduled_to_send'    => $reminder->scheduled_to_send,
            'amount_to_charge'     => $reminder->amount_to_charge,
            'subject'              => (string)$reminder->subject,
            'message'              => (string)$reminder->message
        ];
    }

}
