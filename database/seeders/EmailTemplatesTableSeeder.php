<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('email_templates')->delete();
        
        DB::table('email_templates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'custom1',
                'subject' => 'Custom 1 Subject',
                'message' => 'Custom 1 body',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            1 => 
            array (
                'id' => 2,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'custom2',
                'subject' => 'Custom 2 Subject',
                'message' => 'Custom 2 body',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            2 => 
            array (
                'id' => 3,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'custom3',
                'subject' => 'Custom 3 Subject',
                'message' => 'Custom 3 body',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            3 => 
            array (
                'id' => 4,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'statement',
                'subject' => 'texts.statement_subject',
                'message' => 'texts.statement_body',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            4 => 
            array (
                'id' => 5,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'invoice',
                'subject' => 'A new invoice has been created',
                'message' => 'To view the invoice please click on the button below',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            5 => 
            array (
                'id' => 6,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'quote',
                'subject' => 'A new quote has been created',
                'message' => 'A new quote has been created',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            6 => 
            array (
                'id' => 7,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'credit',
                'subject' => 'A new credit has been created',
                'message' => 'A new credit has been created',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            7 => 
            array (
                'id' => 8,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'payment',
                'subject' => 'Payment Received',
                'message' => 'A new payment has been created',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            8 => 
            array (
                'id' => 9,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'lead',
                'subject' => 'Lead Received',
                'message' => 'A new lead has been created',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            9 => 
            array (
                'id' => 10,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'order',
                'subject' => 'Order Confirmation',
                'message' => 'A new order has been created',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            10 => 
            array (
                'id' => 11,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'payment_partial',
                'subject' => 'Partial Payment Received',
                'message' => 'A new partial payment has been created',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            11 => 
            array (
                'id' => 12,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'order_received',
                'subject' => 'Order Received',
                'message' => 'Your Order has been received. Many Thanks.',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            12 => 
            array (
                'id' => 13,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'order_sent',
                'subject' => 'Order Sent',
                'message' => 'Your Order has been sent, Many Thanks.',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            13 => 
            array (
                'id' => 14,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'case',
                'subject' => 'A new case has been created',
                'message' => 'To view the case please click on the button below',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            14 => 
            array (
                'id' => 15,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'task',
                'subject' => 'A new task has been created',
                'message' => 'To view the task please click on the button below',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            15 => 
            array (
                'id' => 16,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'deal',
                'subject' => 'A new deal has been created',
                'message' => 'To view the deal please click on the button below',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            16 => 
            array (
                'id' => 17,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'purchase_order',
                'subject' => 'A new Purchase Order has been created',
                'message' => 'To view the purchase order please click on the button below',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
            17 => 
            array (
                'id' => 18,
                'account_id' => 1,
                'user_id' => 5,
                'template' => 'endless',
                'subject' => 'texts.reminder_endless_subject',
                'message' => 'Reminder 3 body',
                'amount_to_charge' => '0.00',
                'frequency_id' => 0,
                'percent_to_charge' => '0.00',
                'enabled' => 1,
                'created_at' => '2021-05-26 14:06:02',
                'updated_at' => '2021-05-26 14:52:58',
            ),
        ));
        
        
    }
}