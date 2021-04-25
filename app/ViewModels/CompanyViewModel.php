<?php


namespace App\ViewModels;


use App\Models\Company;

class CompanyViewModel extends ViewModel
{

    /**
     * @var Company
     */
    private Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->company->name ?: '';
    }

    public function logo()
    {
        return iconv_strlen($this->company->company_logo > 0) ? $this->company->company_logo : '';
    }

    public function address()
    {
        $fields = ['address1', 'address2', 'city', 'country', 'phone_number', 'email'];
        $str = '';

        foreach ($fields as $field) {
            if ($field === 'country') {
                $str .= $this->company->country->name . '<br/>';
                continue;
            }
            if (empty($this->company->{$field})) {
                continue;
            }


            $str .= e($this->company->{$field}) . '<br/>';
        }

        return $str;
    }
}