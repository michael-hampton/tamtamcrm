<?php

namespace App\Settings;

use App\Models\Credit;
use App\Models\Invoice;

class BaseSettings
{

    protected array $validationFailures = [];

    protected array $account_settings = [
        'case_forwarding_enabled'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'send_overdue_case_email'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'default_case_assignee'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => null,
            'type'             => 'int'
        ],
        'case_template_new'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => null,
            'type'             => 'int'
        ],
        'case_template_open'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => null,
            'type'             => 'int'
        ],
        'case_template_closed'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => null,
            'type'             => 'int'
        ],
        'lead_forwarding_enabled'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'show_transaction_fee'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'order_charge_point'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'on_creation',
            'type'             => 'string'
        ],
        'show_shipping_cost'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'show_gateway_fee'                     => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'show_tax_rate1'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'show_tax_rate2'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'show_tax_rate3'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'show_tasks_onload'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'include_times_on_invoice'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'task_automation_enabled'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'buy_now_links_enabled'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'task_rate'                            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => null,
            'type'             => 'int'
        ],
        'should_lock_invoice'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'off',
            'type'             => 'string'
        ],
        'should_send_email_for_manual_payment' => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'expense_approval_required'            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'expense_auto_create_invoice'          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'create_expense_invoice'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'create_expense_payment'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'calculate_expense_taxes'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'expense_taxes_calculated_by_amount'   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'expenses_have_inclusive_taxes'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'credit_payments_enabled'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'convert_expense_currency'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'include_expense_documents'            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'include_task_documents'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_send_email_for_online_payment' => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'autobilling_enabled'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'portal_documents_enabled'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'portal_dashboard_enabled'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'portal_tasks_enabled'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'portal_cases_enabled'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'display_invoice_terms'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'display_quote_terms'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'display_invoice_signature'            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'display_quote_signature'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'portal_terms'                         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'portal_privacy_policy'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'charge_gateway_to_customer'           => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'create_task_on_order'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'slack_enabled'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'portal_dashboard_message'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'percent_to_charge_endless'            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'allow_backorders'                     => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'default_case_priority'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'allow_partial_orders'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'inventory_enabled'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'amount_to_charge_endless'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'should_email_invoice'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_email_quote'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_email_purchase_order'          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_email_deal'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_email_order'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'reminder_send_time'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 32400,
            'type'             => 'int'
        ],
        'invoice_payment_deleted_status'       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => Invoice::STATUS_SENT,
            'type'             => 'int'
        ],
        'credit_payment_deleted_status'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => Credit::STATUS_SENT,
            'type'             => 'int'
        ],
        'email_sending_method'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'default',
            'type'             => 'string'
        ],
        'currency_id'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 2,
            'type'             => 'string'
        ],
        'counter_number_applied'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'when_saved',
            'type'             => 'string'
        ],
        'quote_number_applied'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'when_saved',
            'type'             => 'string'
        ],
        'reminder1_enabled'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'reminder2_enabled'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'reminder3_enabled'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'number_of_days_after_1'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'int'
        ],
        'number_of_days_after_2'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'int'
        ],
        'number_of_days_after_3'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'int'
        ],
        'scheduled_to_send_1'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'scheduled_to_send_2'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'scheduled_to_send_3'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'amount_to_charge_1'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'amount_to_charge_2'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'amount_to_charge_3'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'percent_to_charge_1'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'percent_to_charge_2'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'percent_to_charge_3'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'endless_reminder_frequency_id'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'integer'
        ],
        'document_email_attachment'            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'enable_email_markup'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'email_template_statement'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_task'                  => [
            'required'         => false,
            'translated_value' => 'texts.task_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_deal'                  => [
            'required'         => false,
            'translated_value' => 'texts.deal_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_case'                  => [
            'required'         => false,
            'translated_value' => 'texts.case_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_statement'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_task'                   => [
            'required'         => false,
            'translated_value' => 'texts.task_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_deal'                   => [
            'required'         => false,
            'translated_value' => 'texts.deal_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_case'                   => [
            'required'         => false,
            'translated_value' => 'texts.case_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'show_signature_on_pdf'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'quote_footer'                         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'order_footer'                         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'page_size'                            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'A4',
            'type'             => 'string'
        ],
        'font_size'                            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 12,
            'type'             => 'int'
        ],
        'primary_font'                         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'Roboto',
            'type'             => 'string'
        ],
        'secondary_font'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'Roboto',
            'type'             => 'string'
        ],
        'embed_documents'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'all_pages_header'                     => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'dont_display_empty_pdf_columns'       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'all_pages_footer'                     => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'task_number_prefix'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'task_counter_type'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'task_number_counter'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'project_number_prefix'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'project_counter_type'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'project_number_counter'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'expense_number_prefix'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'expense_counter_type'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'expense_number_counter'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'company_number_prefix'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'company_counter_type'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'company_number_counter'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'lead_number_prefix'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'lead_counter_type'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'lead_number_counter'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'case_number_prefix'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'case_counter_type'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'case_number_counter'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'payment_number_prefix'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'payment_counter_type'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'payment_number_counter'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'reply_to_email'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'bcc_email'                            => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'pdf_email_attachment'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'ubl_email_attachment'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'email_style'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'light',
            'type'             => 'string'
        ],
        'email_style_custom'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'light',
            'type'             => 'string'
        ],
        'address1'                             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'address2'                             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'city'                                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'company_logo'                         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'country_id'                           => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 225,
            'type'             => 'string'
        ],
        'customer_number_prefix'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'customer_counter_type'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'customer_number_counter'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'credit_number_prefix'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'credit_counter_type'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'credit_number_counter'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'order_number_prefix'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'order_counter_type'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'order_number_counter'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'recurringinvoice_number_prefix'       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'recurringinvoice_counter_type'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'recurringinvoice_number_counter'      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'recurringquote_number_prefix'         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'recurringquote_counter_type'          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'recurringquote_number_counter'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'custom_value1'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'custom_value2'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'custom_value3'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'custom_value4'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'default_task_rate'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'float'
        ],
        'email_signature'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'inclusive_taxes'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'invoice_number_prefix'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'invoice_counter_type'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'invoice_number_counter'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'invoice_design_id'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'invoice_footer'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'invoice_labels'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'invoice_terms'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'credit_footer'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'credit_terms'                         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'order_terms'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'name'                                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'Untitled1',
            'type'             => 'string'
        ],
        'payment_terms'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 30,
            'type'             => 'integer'
        ],
        'payment_type_id'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 0,
            'type'             => 'string'
        ],
        'phone'                                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'postal_code'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'quote_design_id'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'purchase_order_design_id'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'credit_design_id'                     => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'order_design_id'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'case_design_id'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'task_design_id'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'deal_design_id'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'lead_design_id'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'string'
        ],
        'type_id'                              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'int'
        ],
        'quote_number_prefix'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'quote_counter_type'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'quote_number_counter'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'deal_number_prefix'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'deal_counter_type'                    => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'deal_number_counter'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'purchaseorder_number_prefix'          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'purchaseorder_counter_type'           => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'purchaseorder_number_counter'         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 1,
            'type'             => 'integer'
        ],
        'quote_terms'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'recurring_number_prefix'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'R',
            'type'             => 'string'
        ],
        'state'                                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email'                                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'vat_number'                           => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'number'                               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'date_format'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'DD/MMM/YYYY',
            'type'             => 'string'
        ],
        'language_id'                          => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'show_currency_code'                   => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'send_reminders'                       => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'should_archive_invoice'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'should_archive_quote'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_convert_quote'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_convert_order'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_archive_order'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_archive_lead'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_archive_purchase_order'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_archive_deal'                  => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'has_minimum_quantity'                 => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'quantity_can_be_changed'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'convert_product_currency'             => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'fill_products'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_update_inventory'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
        'should_update_products'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'shared_invoice_quote_counter'         => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'date_counter_next_reset'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'counter_frequency_type'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'MONTHLY',
            'type'             => 'string'
        ],
        'counter_padding'                      => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 4,
            'type'             => 'integer'
        ],
        'design'                               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => 'views/pdf/design1.blade.php',
            'type'             => 'string'
        ],
        'website'                              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'pdf_variables'                        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => [],
            'type'             => 'object'
        ],
        'email_subject_custom1'                => [
            'required'         => false,
            'translated_value' => 'texts.custom1_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_custom2'                => [
            'required'         => false,
            'translated_value' => 'texts.custom2_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_custom3'                => [
            'required'         => false,
            'translated_value' => 'texts.custom3_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_custom1'               => [
            'required'         => false,
            'translated_value' => 'texts.custom1_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_custom2'               => [
            'required'         => false,
            'translated_value' => 'texts.custom2_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_invoice'                => [
            'required'         => false,
            'translated_value' => 'texts.invoice_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_quote'                  => [
            'required'         => false,
            'translated_value' => 'texts.quote_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_purchase_order'         => [
            'required'         => false,
            'translated_value' => 'texts.purchase_order_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_credit'                 => [
            'required'         => false,
            'translated_value' => 'texts.credit_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_payment'                => [
            'required'         => false,
            'translated_value' => 'texts.payment_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_lead'                   => [
            'required'         => false,
            'translated_value' => 'texts.lead_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_order_received'         => [
            'required'         => false,
            'translated_value' => 'texts.order_received_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_order_sent'             => [
            'required'         => false,
            'translated_value' => 'texts.order_sent_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_subject_payment_partial'        => [
            'required'         => false,
            'translated_value' => 'texts.partial_payment_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_invoice'               => [
            'required'         => false,
            'translated_value' => 'texts.invoice_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_quote'                 => [
            'required'         => false,
            'translated_value' => 'texts.quote_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_purchase_order'        => [
            'required'         => false,
            'translated_value' => 'texts.purchase_order_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_credit'                => [
            'required'         => false,
            'translated_value' => 'texts.credit_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_payment'               => [
            'required'         => false,
            'translated_value' => 'texts.payment_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_lead'                  => [
            'required'         => false,
            'translated_value' => 'texts.lead_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_order_received'        => [
            'required'         => false,
            'translated_value' => 'texts.order_received_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_order_sent'            => [
            'required'         => false,
            'translated_value' => 'texts.order_sent_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_payment_partial'       => [
            'required'         => false,
            'translated_value' => 'texts.partial_payment_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'reminder1_subject'                    => [
            'required'         => false,
            'translated_value' => 'texts.reminder1_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'reminder2_subject'                    => [
            'required'         => false,
            'translated_value' => 'texts.reminder2_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'reminder3_subject'                    => [
            'required'         => false,
            'translated_value' => 'texts.reminder3_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'endless_reminder_subject'             => [
            'required'         => false,
            'translated_value' => 'texts.endless_subject',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'reminder1_message'                    => [
            'required'         => false,
            'translated_value' => 'texts.reminder1_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'reminder2_message'                    => [
            'required'         => false,
            'translated_value' => 'texts.reminder2_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'reminder3_message'                    => [
            'required'         => false,
            'translated_value' => 'texts.reminder3_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_reminder_endless'      => [
            'required'         => false,
            'translated_value' => 'texts.endless_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'email_template_custom3'               => [
            'required'         => false,
            'translated_value' => 'texts.custom3_body',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'under_payments_allowed'               => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'over_payments_allowed'                => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => false,
            'type'             => 'bool'
        ],
        'minimum_amount_required'              => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => '',
            'type'             => 'string'
        ],
        'require_customer_portal_login'        => [
            'required'         => false,
            'translated_value' => '',
            'default_value'    => true,
            'type'             => 'bool'
        ],
    ];

    protected function validate($saved_settings, $actual_settings)
    {
        if (empty($saved_settings)) {
            return false;
        }

        foreach ($actual_settings as $key => $actual_setting) {
            if (!isset($saved_settings->$key)) {
                $saved_settings->{$key} = !empty($actual_setting['translated_value']) ? trans(
                    $actual_setting['translated_value']
                ) : $actual_setting['default_value'];
            }

            // if required and empty
            if (empty($saved_settings->{$key}) && $saved_settings->{$key} !== false && $actual_setting['required'] === true) {
                $this->validationFailures[] = "{$key} is a required field";
            }

            if ($actual_setting['type'] === 'bool' && isset($saved_settings->{$key}) && is_string(
                    $saved_settings->{$key}
                )) {
                if (in_array($saved_settings->{$key}, ['on', 'off'])) {
                    $saved_settings->{$key} = $saved_settings->{$key} === 'on';
                }

                if (in_array($saved_settings->{$key}, ['true', 'false'])) {
                    $saved_settings->{$key} = $saved_settings->{$key} === 'true';
                }
            }

            // if value empty and has default value then use default
            if (!is_bool(
                    $saved_settings->{$key}
                ) && $saved_settings->{$key} === '' && !empty($actual_setting['default_value'])) {
                $saved_settings->{$key} = !empty($actual_setting['translated_value']) ? trans(
                    $actual_setting['translated_value']
                ) : $actual_setting['default_value'];
            }

            // cast type
            settype($saved_settings->{$key}, $actual_setting['type']);
        }

        if (count($this->validationFailures) > 0) {
            echo '<pre>';
            print_r($this->validationFailures);
            die;
            die('here');
            return false;
        }

        return $saved_settings;
    }
}
