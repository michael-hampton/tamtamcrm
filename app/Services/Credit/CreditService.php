<?php

namespace App\Services\Credit;

use App\Components\Pdf\InvoicePdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Credit;
use App\Services\ServiceBase;
use ReflectionException;

class CreditService extends ServiceBase
{
    /**
     * @var Credit
     */
    protected Credit $credit;

    /**
     * CreditService constructor.
     * @param Credit $credit
     */
    public function __construct(Credit $credit)
    {
        parent::__construct($credit);
        $this->credit = $credit;
    }

    /**
     * @param null $contact
     * @param bool $update
     * @return mixed|string
     * @throws ReflectionException
     */
    public function generatePdf($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->credit->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new InvoicePdf($this->credit)), $this->credit, $contact, $update);
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return Credit|null
     */
    public function sendEmail($contact = null, $subject, $body, $template = 'credit'): ?Credit
    {
        if (!$this->sendInvitationEmails($subject, $body, $template, $contact)) {
            return null;
        }

        return $this->credit;
    }

    public function calculateInvoiceTotals(): Credit
    {
        return $this->calculateTotals($this->credit);
    }
}
