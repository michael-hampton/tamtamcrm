<?php


namespace App\ViewModels;


use App\Models\Account;
use App\Models\Country;

class AccountViewModel extends ViewModel
{
    /**
     * @var Account
     */
    private Account $account;

    /**
     * AccountViewModel constructor.
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->account->name ?: '';
    }

    public function logo($settings = null)
    {
        if (!$settings) {
            $settings = $this->account->settings;
        }

        if (empty($settings) || empty($settings->company_logo)) {
            return '';
        }

        return url($settings->company_logo);
    }

    public function address($settings = null)
    {
        $str = '';
        $fields = ['address1', 'address2', 'city', 'country_id', 'phone', 'email'];

        if (!$settings) {
            $settings = $this->account->settings;
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