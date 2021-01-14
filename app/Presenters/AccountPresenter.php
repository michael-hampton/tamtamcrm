<?php

namespace App\Presenters;

use App\Models\Country;

/**
 * Class AccountPresenter
 * @package App\Models\Presenters
 */
class AccountPresenter extends EntityPresenter
{
    /**
     * @return string
     */
    public function name()
    {
        $settings = $this->entity->settings;

        return $this->settings->name ?: '';
    }

    public function logo($settings = null)
    {
        if (!$settings) {
            $settings = $this->entity->settings;
        }

        if (empty($settings) || empty($settings->company_logo)) {
            return '';
        }

        return url($settings->company_logo);
    }

    public function address($settings = null)
    {
        $str = '';
        $company = $this->entity;
        $fields = ['address1', 'address2', 'city', 'country_id', 'phone', 'email'];

        if (!$settings) {
            $settings = $this->entity->settings;
        }

        foreach ($fields as $field) {
            if (empty($settings->{$field})) {
                continue;
            }

            if ($field === 'country_id') {
                $country = Country::where('id', $settings->country_id)->first();
                $str .= e($country->name) . '<br/>';
                continue;
            }

            $str .= $settings->{$field} . '<br/>';
        }

        return $str;
    }
}
