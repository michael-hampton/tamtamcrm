<?php

namespace App\Events\Cases;

use App\Models\Cases;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class CaseWasArchived
{
    use SerializesModels;

    /**
     * @var case|Cases
     */
    public Cases $case;

    /**
     * Create a new event instance.
     *
     * @param Cases $case $case
     */
    public function __construct(Cases $case)
    {
        $this->case = $case;
    }
}
