<?php


namespace App\ViewModels;


use App\Models\CompanyContact;
use App\Models\CustomerContact;

class CompanyContactViewModel extends ViewModel
{
    /**
     * @var CompanyContact 
     */
    private CompanyContact $company_contact;

    /**
     * CompanyContactViewModel constructor.
     * @param CompanyContact $company_contact
     */
    public function __construct(CompanyContact $company_contact)
    {
        $this->company_contact = $company_contact;
    }

    /**
     * @return string
     */
    public function name()
    {
        if (!empty($this->company_contact->first_name) && !empty($this->company_contact->last_name)) {
            return $this->company_contact->first_name . ' ' . $this->company_contact->last_name;
        }

        return $this->company_contact->customer->name ?: '';
    }
}