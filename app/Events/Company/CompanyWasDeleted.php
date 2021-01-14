<?php

namespace App\Events\Company;

use App\Models\Company;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class CompanyWasDeleted
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Company
     */
    public Company $company;

    /**
     * CompanyWasDeleted constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->send($company, get_class($this));
    }
}
