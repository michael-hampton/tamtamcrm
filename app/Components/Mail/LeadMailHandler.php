<?php

namespace App\Components\Mail;

use App\Factory\LeadFactory;
use App\Models\Lead;
use App\Repositories\LeadRepository;
use BeyondCode\Mailbox\InboundEmail;

class LeadMailHandler
{
    public function __invoke(InboundEmail $email)
    {
        $names = explode(' ', $email->fromName());

        $data = ['name' => $email->subject(), 'email' => $email->from()];

        if (!empty($names)) {
            $data['first_name'] = $names[0];
            $data['last_name'] = $names[1];
        }

        $data['description'] = $email->text();

        $lead = LeadFactory::create(auth()->user()->account_user()->account, auth()->user());
        $lead = (new LeadRepository(new Lead()))->save($data, $lead);

        return $lead;
    }
}
