<?php

namespace App\Events\Invoice;

use App\Models\Invitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasEmailed.
 */
class InvoiceWasEmailed
{
    use SerializesModels;

    /**
     * @var Invitation
     */
    public Invitation $invitation;

    public string $template;

    /**
     * InvoiceWasEmailed constructor.
     * @param Invitation $invitation
     * @param string $template
     */
    public function __construct(Invitation $invitation, string $template = '')
    {
        $this->invitation = $invitation;
        $this->template = $template;
    }
}
