<?php

namespace App\Presenters;

/**
 * Class CompanyPresenter
 * @package App\Models\Presenters
 */
class CompanyPresenter extends EntityPresenter
{
    /**
     * @return string
     */
    public function name()
    {
        return $this->entity->name ?: '';
    }

    public function logo()
    {
        return iconv_strlen(
            $this->entity->company_logo >
            0
        ) ? $this->entity->company_logo : '';
    }

    public function address()
    {
        $fields = ['address1', 'address2', 'city', 'country', 'phone_number', 'email'];
        $str = '';

        foreach ($fields as $field) {
            if (empty($company->{$field})) {
                continue;
            }

            if ($field === 'country') {
                $str .= $country->name . '<br/>';
                continue;
            }

            $str .= e($$company->{$field}) . '<br/>';
        }

        return $str;
    }
}
