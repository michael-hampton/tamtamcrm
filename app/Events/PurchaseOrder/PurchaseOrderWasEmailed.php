<?php

namespace App\Events\PurchaseOrder;

use App\Models\Invitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailed.
 */
class PurchaseOrderWasEmailed
{
    use SerializesModels;

    /**
     * @var Invitation
     */
    public Invitation $invitation;

    public string $template;

    /**
     * PurchaseOrderWasEmailed constructor.
     * @param Invitation $invitation
     * @param string $template
     */
    public function __construct(Invitation $invitation, string $template = '')
    {
        $this->invitation = $invitation;
        $this->template = $template;
    }
}
