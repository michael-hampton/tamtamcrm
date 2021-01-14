<?php

namespace App\Services\Deal;

use App\Jobs\Email\SendEmail;
use App\Models\deal;
use App\Traits\MakesInvoiceHtml;

class DealEmail
{
    use MakesInvoiceHtml;

    /**
     * @var deal
     */
    private Deal $deal;

    /**
     * @var string
     */
    private $subject = '';

    /**
     * @var string
     */
    private $body = '';

    /**
     * dealEmail constructor.
     * @param deal $deal
     * @param string $subject
     * @param string $body
     */
    public function __construct(Deal $deal, $subject = '', $body = '')
    {
        $this->deal = $deal;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Builds the correct template to send
     * @return void
     */
    public function execute()
    {
        $subject = strlen($this->subject) > 0 ? $this->subject : $this->deal->account->getSetting('email_subject_deal');
        $body = strlen($this->body) > 0 ? $this->body : $this->deal->account->getSetting('email_template_deal');

        SendEmail::dispatchNow($this->deal, $subject, $body, 'deal', $this->deal->customer->contacts->first());
    }
}
