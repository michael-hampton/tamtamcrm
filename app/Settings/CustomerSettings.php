<?php

namespace App\Settings;

use App\Models\Customer;
use Exception;

class CustomerSettings extends BaseSettings
{
    private $settings = [
        'payment_terms'           => ['required' => true, 'default_value' => -1, 'type' => 'integer'],
        'payment_type_id'         => ['required' => false, 'default_value' => 0, 'type' => 'string'],
        'customer_number_counter' => ['required' => false, 'default_value' => 0, 'type' => 'string'],
        'customer_number_pattern' => ['required' => false, 'default_value' => '', 'type' => 'string'],
        'language_id'             => ['required' => false, 'default_value' => 1, 'type' => 'string']
    ];

    public function save(Customer $customer, $settings): ?Customer
    {
        try {
            $settings = $this->validate($settings, array_merge($this->account_settings, $this->settings));

            if (!$settings) {
                return null;
            }

            $customer->settings = $settings;
            $customer->save();

            return $customer;
        } catch (Exception $e) {
            echo $e->getMessage();
            die('here 55');
        }
    }

    public function getCustomerDefaults()
    {
        return (object)array_filter(
            array_combine(array_keys($this->settings), array_column($this->settings, 'default_value'))
        );
    }

    /**
     * @param $client_settings
     * @param $account_settings
     * @return object
     */
    public function buildCustomerSettings($client_settings, $account_settings)
    {
        if (!$client_settings) {
            return $account_settings;
        }

        foreach ($account_settings as $key => $value) {
            if (((property_exists($client_settings, $key) && is_string($client_settings->{$key}) &&
                    (iconv_strlen($client_settings->{$key}) < 1))) ||
                !isset($client_settings->{$key}) && property_exists($account_settings, $key)) {
                $client_settings->{$key} = $account_settings->{$key};
            }
        }

        return $client_settings;
    }
}
