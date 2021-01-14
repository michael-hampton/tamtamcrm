<?php

namespace App\Events\Cases;

use App\Models\Cases;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class CaseWasEmailed
{
    use SerializesModels;

    /**
     * @var Cases
     */
    public Cases $case;

    /**
     * CaseWasEmailed constructor.
     * @param Cases $case
     */
    public function __construct(Cases $case)
    {
        $this->case = $case;
    }
}
