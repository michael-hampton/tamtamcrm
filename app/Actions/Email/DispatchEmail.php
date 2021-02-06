<?php

namespace App\Actions\Email;


use App\Models\Invoice;

class DispatchEmail extends BaseEmailActions
{
    public function __construct($entity)
    {
        parent::__construct($entity);
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return Invoice|null
     */
    public function execute($contact = null, $subject, $body, $template = 'invoice')
    {
        if (!$this->sendInvitationEmails($subject, $body, $template, $contact)) {
            return null;
        }

        return $this->entity;
    }
}