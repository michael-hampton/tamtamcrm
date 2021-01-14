<?php

namespace App\Settings;

use App\Models\Account;
use Exception;

class AccountSettings extends BaseSettings
{
    private $settings = [
        'currency_id' => [
            'required'         => true,
            'translated_value' => '',
            'default_value'    => 2,
            'type'             => 'string'
        ],
        'address1'    => [
            'required'         => true,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'city'        => [
            'required'         => true,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email'       => [
            'required'         => true,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
    ];

    public function __construct()
    {
        $this->account_settings['pdf_variables']['default_value'] = $this->getPdfVariables();
    }

    private function getPdfVariables()
    {
        $variables = [
            'customer_details' => [
                '$customer.name',
                '$customer.number',
                '$customer.vat_number',
                '$customer.address1',
                '$customer.address2',
                '$customer.city_state_postal',
                '$customer.country',
                '$contact.email',
            ],
            'account_details'  => [
                '$account.name',
                '$account.number',
                '$account.vat_number',
                '$account.website',
                '$account.email',
                '$account.phone',
            ],
            'account_address'  => [
                '$account.address1',
                '$account.address2',
                '$account.city_state_postal',
                '$account.country',
            ],
            'invoice'          => [
                '$invoice.invoice_number',
                '$invoice.po_number',
                '$invoice.invoice_datetime',
                '$invoice.invoice_agent',
                '$invoice.invoice_date',
                '$invoice.due_date',
                '$invoice.balance_due',
                '$invoice.invoice_total',
            ],
            'lead'             => [

            ],
            'case'             => [

            ],
            'task'             => [

            ],
            'deal'             => [

            ],
            'order'            => [
                '$order.order_number',
                '$order.po_number',
                '$order.order_date',
                '$order.order_datetime',
                '$order.order_agent',
                '$order.due_date',
                '$order.balance_due',
                '$order.order_total',
            ],
            'quote'            => [
                '$quote.quote_number',
                '$quote.po_number',
                '$quote.quote_datetime',
                '$quote.quote_agent',
                '$quote.quote_date',
                '$quote.valid_until',
                '$quote.balance_due',
                '$quote.quote_total',
            ],
            'purchase_order'   => [
                '$purchaseorder.quote_number',
                '$purchaseorder.po_number',
                '$purchaseorder.purchaseorder_datetime',
                '$purchaseorder.purchaseorder_agent',
                '$purchaseorder.purchaseorder_date',
                '$purchaseorder.valid_until',
                '$purchaseorder.balance_due',
                '$purchaseorder.quote_total',
            ],

            'credit'                => [
                '$credit.credit_number',
                '$credit.po_number',
                '$credit.credit_datetime',
                '$credit.credit_agent',
                '$credit.credit_date',
                '$credit.credit_balance',
                '$credit.credit_amount',
            ],
            'product_columns'       => [
                '$product.product_key',
                '$product.notes',
                '$product.cost',
                '$product.quantity',
                '$product.discount',
                '$product.tax',
                '$product.line_total',
            ],
            'task_columns'          => [
                '$task.product_key',
                '$task.notes',
                '$task.cost',
                '$task.quantity',
                '$task.discount',
                '$task.tax',
                '$task.line_total',
            ],
            'dispatch_note_columns' => [
                '$product.product_key',
                '$product.notes',
                '$product.quantity'
            ],
            'dispatch_note'         => [
                '$invoice.number',
                '$invoice.po_number',
                '$invoice.invoice_date',
                '$invoice.invoice_total'
            ],
        ];

        return json_decode(json_encode($variables));
    }

    public function getAccountDefaults()
    {
        $defaults = array_combine(
            array_keys($this->account_settings),
            array_column($this->account_settings, 'default_value')
        );

        $translated = array_filter(
            array_combine(
                array_keys($this->account_settings),
                array_column($this->account_settings, 'translated_value')
            )
        );
        $translated = array_map(array($this, 'translate'), $translated);

        return (object)array_merge($defaults, $translated);
    }

    /**
     * @param Account $account
     * @param $settings
     * @param bool $full_validation
     * @return Account
     */
    public function save(Account $account, $settings, $full_validation = false): Account
    {
        try {
            $settings = $this->validate($settings, array_merge($this->account_settings, $this->settings));

            if (!$settings && $full_validation === true) {
                return false;
            }

            $account->settings = $settings;
            $account->save();

            return $account;
        } catch (Exception $e) {
            echo $e->getMessage();
            die('here');
        }
    }

    private function translate($value)
    {
        return trans($value);
    }
}
