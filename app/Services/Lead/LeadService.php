<?php

namespace App\Services\Lead;

use App\Components\Pdf\LeadPdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Lead;
use App\Repositories\LeadRepository;
use App\Services\ServiceBase;
use ReflectionException;

/**
 * Class TaskService
 * @package App\Services\Task
 */
class LeadService extends ServiceBase
{
    /**
     * @var Lead
     */
    protected Lead $lead;

    /**
     * LeadService constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $config = [
            'email'   => $lead->account->getSetting('should_email_lead'),
            'archive' => $lead->account->getSetting('should_archive_lead')
        ];

        parent::__construct($lead);
        $this->lead = $lead;
    }

    /**
     * @return Lead
     */
    public function convertLead(): Lead
    {
        $lead = (new ConvertLead($this->lead))->execute();

        // trigger
        $subject = trans('texts.lead_converted_subject');
        $body = trans('texts.lead_converted_body');
        $this->trigger($subject, $body, new LeadRepository(new Lead));

        return $lead;
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return void
     */
    public function sendEmail($contact = null, $subject = '', $body = '', $template = 'lead')
    {
        return (new LeadEmail($this->lead, $subject, $body))->execute();
    }

    /**
     * @param null $contact
     * @param bool $update
     * @return mixed|string
     * @throws ReflectionException
     */
    public function generatePdf($contact = null, $update = false)
    {
        return CreatePdf::dispatchNow((new LeadPdf($this->lead)), $this->lead, $contact, $update);
    }

}
