<?php

namespace App\Events\Company;

use App\Models\Company;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class CompanyWasRestored
{
    use SerializesModels;

    /**
     * @var Company
     */
    public Company $company;

    /**
     * CompanyWasRestored constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }
}
