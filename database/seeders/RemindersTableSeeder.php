<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemindersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('reminders')->delete();

        DB::table('reminders')->insert(array(
            0 =>
                array(
                    'id'                   => 3,
                    'account_id'           => 1,
                    'user_id'              => 5,
                    'enabled'              => 1,
                    'number_of_days_after' => 30,
                    'scheduled_to_send'    => 'after_invoice_date',
                    'amount_to_charge'     => '20.00',
                    'amount_type'          => 'fixed',
                    'created_at'           => '2021-05-26 09:32:22',
                    'updated_at'           => '2021-05-26 09:32:22',
                    'subject'              => trans('texts.reminder1_subject'),
                    'message'              => trans('texts.reminder1_body'),
                ),
            1 =>
                array(
                    'id'                   => 4,
                    'account_id'           => 1,
                    'user_id'              => 5,
                    'enabled'              => 1,
                    'number_of_days_after' => 30,
                    'scheduled_to_send'    => 'before_due_date',
                    'amount_to_charge'     => '8.00',
                    'amount_type'          => 'fixed',
                    'created_at'           => '2021-05-26 09:32:22',
                    'updated_at'           => '2021-05-26 09:32:22',
                    'subject'              => trans('texts.reminder2_subject'),
                    'message'              => trans('texts.reminder2_body'),
                ),
            2 =>
                array(
                    'id'                   => 5,
                    'account_id'           => 1,
                    'user_id'              => 5,
                    'enabled'              => 1,
                    'number_of_days_after' => 30,
                    'scheduled_to_send'    => 'before_due_date',
                    'amount_to_charge'     => '8.00',
                    'amount_type'          => 'fixed',
                    'created_at'           => '2021-05-26 09:32:22',
                    'updated_at'           => '2021-05-26 09:32:22',
                    'subject'              => trans('texts.reminder3_subject'),
                    'message'              => trans('texts.reminder3_body'),
                ),
        ));


    }
}