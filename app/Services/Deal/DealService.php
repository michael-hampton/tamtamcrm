<?php

namespace App\Services\Deal;

use App\Components\Pdf\TaskPdf;
use App\Jobs\Email\SendEmail;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Deal;
use App\Services\ServiceBase;
use ReflectionException;

/**
 * Class TaskService
 * @package App\Services\Task
 */
class DealService extends ServiceBase
{
    /**
     * @var Deal
     */
    protected Deal $deal;

    /**
     * DealService constructor.
     * @param Deal $deal
     */
    public function __construct(Deal $deal)
    {
        $config = [
            'email'   => $deal->account->getSetting('should_email_lead'),
            'archive' => $deal->account->getSetting('should_archive_lead')
        ];

        parent::__construct($deal);
        $this->deal = $deal;
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return void
     */
    public function sendEmail($contact = null, $subject = '', $body = '', $template = 'deal')
    {
        $subject = !empty($subject) ? $subject : $this->deal->account->getSetting('email_subject_deal');
        $body = !empty($body) ? $body : $this->deal->account->getSetting('email_template_deal');

        SendEmail::dispatchNow($this->deal, $subject, $body, 'deal', $this->deal->customer->contacts->first());
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
            $contact = $this->deal->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new TaskPdf($this->deal)), $this->deal, $contact, $update);
    }

}
