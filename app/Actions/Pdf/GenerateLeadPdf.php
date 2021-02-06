<?php

namespace App\Actions\Pdf;


use App\Components\Pdf\LeadPdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Lead;

class GenerateLeadPdf
{
    private Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function execute($contact = null, $update = false)
    {
        return CreatePdf::dispatchNow((new LeadPdf($this->lead)), $this->lead, $contact, $update);
    }
}