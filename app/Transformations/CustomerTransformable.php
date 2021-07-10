<?php

namespace App\Transformations;

use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerGateway;
use App\Models\ErrorLog;
use App\Models\File;
use App\Models\Transaction;
use Exception;

trait CustomerTransformable
{
    /**
     * @param Customer $customer
     * @return array
     * @throws Exception
     */
    protected function transformCustomer(Customer $customer, $customer_contacts = null, $files = null)
    {
        $company = !empty($customer->company_id) ? $customer->company->toArray() : '';

        $billing = $this->transformAddress($customer, true);
        $shipping = $this->transformAddress($customer);

        $contacts = [];

        if ($customer_contacts !== null) {
            if (!empty($customer_contacts[$customer->id])) {
                $contacts = $this->transformContacts($customer_contacts[$customer->id]);
            }
        } else {
            $contacts = $this->transformContacts($customer->contacts);
        }

        return [
            'id'                     => (int)$customer->id,
            'user_id'                => (int)$customer->user_id,
            'number'                 => $customer->number,
            'created_at'             => $customer->created_at,
            'name'                   => $customer->name ?: '',
            'phone'                  => $customer->phone ?: '',
            'company_id'             => $customer->company_id,
            'deleted_at'             => $customer->deleted_at,
            'company'                => $company,
            'credit'                 => $customer->credit_balance ?: 0,
            'contacts'               => $contacts,
            'default_payment_method' => $customer->default_payment_method,
            'group_settings_id'      => $customer->group_settings_id,
            'shipping'               => $shipping,
            'billing'                => $billing,
            'country_id'             => !empty($billing) ? $billing['country_id'] : null,
            'website'                => $customer->website ?: '',
            'vat_number'             => $customer->vat_number ?: '',
            'industry_id'            => (int)$customer->industry_id ?: null,
            'size_id'                => (int)$customer->size_id ?: null,
            'currency_id'            => $customer->currency_id,
            'language_id'            => !empty($customer->settings->language_id) ? $customer->settings->language_id : null,
            'balance'                => (float)$customer->balance,
            'amount_paid'            => (float)$customer->amount_paid,
            'credit_balance'         => (float)$customer->credit_balance,
            'assigned_to'            => $customer->assigned_to,
            'settings'               => $customer->settings,
            //'transactions'           => $this->transformTransactions($customer->transactions),
            //'error_logs'             => empty($exclude) || !in_array('logs', $exclude) ? $this->transformErrorLogs($customer->error_logs): [],
            'custom_value1'          => $customer->custom_value1 ?: '',
            'custom_value2'          => $customer->custom_value2 ?: '',
            'custom_value3'          => $customer->custom_value3 ?: '',
            'custom_value4'          => $customer->custom_value4 ?: '',
            'internal_note'          => $customer->internal_note ?: '',
            'customer_note'          => $customer->customer_note ?: '',
            'files'                  => !empty($files) && !empty($files[$customer->id]) ? $this->transformCustomerFiles(
                $files[$customer->id]
            ) : [],
            //'gateway_tokens'         => empty($exclude) || !in_array('gateways', $exclude) ? $this->transformGatewayTokens($customer->gateways) : [],
            'hide'                   => (bool)$customer->hide,
        ];
    }

    /**
     * @param $addresses
     * @return array
     */
    private function transformAddress($customer, $billing = false)
    {
        if ($billing === true) {
            return [
                'address_1'    => !empty($customer->address_1) ? $customer->address_1 : '',
                'address_2'    => !empty($customer->address_2) ? $customer->address_2 : '',
                'zip'          => !empty($customer->zip) ? $customer->zip : '',
                'city'         => !empty($customer->city) ? $customer->city : '',
                'country_id'   => !empty($customer->country_id) ? $customer->country_id : 2,
                'address_type' => 1
            ];
        }

        return [
            'address_1'    => !empty($customer->shipping_address_1) ? $customer->shipping_address_1 : '',
            'address_2'    => !empty($customer->shipping_address_2) ? $customer->shipping_address_2 : '',
            'zip'          => !empty($customer->shipping_zip) ? $customer->shipping_zip : '',
            'city'         => !empty($customer->shipping_city) ? $customer->shipping_city : '',
            'country_id'   => !empty($customer->shipping_country_id) ? $customer->shipping_country_id : 2,
            'address_type' => 2
        ];
    }

    /**
     * @param $contacts
     * @return array
     */
    private function transformContacts($contacts)
    {
        if (empty($contacts)) {
            return [];
        }

        return $contacts->map(
            function (CustomerContact $contact) {
                return (new ContactTransformable())->transformContact($contact);
            }
        )->all();
    }

    private function transformCustomerFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        return $files->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }

    /**
     * @param $transactions
     * @return array
     */
    private function transformTransactions($transactions)
    {
        return [];

        if (empty($transactions)) {
            return [];
        }

        return $transactions->map(
            function (Transaction $transaction) {
                return (new TransactionTransformable())->transformTransaction($transaction);
            }
        )->all();
    }

    /**
     * @param $error_logs
     * @return array
     */
    private function transformErrorLogs($error_logs)
    {
        if (empty($error_logs)) {
            return [];
        }

        return $error_logs->map(
            function (ErrorLog $error_log) {
                return (new ErrorLogTransformable())->transformErrorLog($error_log);
            }
        )->all();
    }

    /**
     * @param $gateway_tokens
     * @return array
     */
    private function transformGatewayTokens($gateway_tokens)
    {
        if (empty($gateway_tokens)) {
            return [];
        }

        return $gateway_tokens->map(
            function (CustomerGateway $gateway) {
                return (new CustomerGatewayTransformable())->transformGateway($gateway);
            }
        )->all();
    }
}
