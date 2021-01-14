<?php

namespace App\Events\Credit;

use App\Models\Credit;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreditWasMarkedSent.
 */
class CreditWasMarkedSent
{
    use SerializesModels;

    /**
     * @var Credit
     */
    public $credit;

    /**
     * CreditWasMarkedSent constructor.
     * @param Credit $credit
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }
}
