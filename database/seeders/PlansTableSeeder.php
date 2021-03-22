<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->delete();
        
        DB::table('plans')->insert(array (
            0 => 
            array (
                'id' => 7,
                'name' => 'Standard Year',
                'code' => 'STDY',
                'description' => 'Standard plan',
                'price' => '200.0000',
                'interval_unit' => 'year',
                'interval_count' => 1,
                'trial_period' => 0,
                'sort_order' => 1,
                'created_at' => '2021-03-21 13:56:46',
                'updated_at' => '2021-03-21 13:56:46',
                'is_active' => 1,
                'signup_fee' => '0',
                'currency' => 'GBP',
                'invoice_period' => 1,
                'invoice_interval' => 'year',
                'grace_period' => 0,
                'grace_interval' => 'day',
                'prorate_day' => NULL,
                'prorate_period' => NULL,
                'prorate_extend_due' => NULL,
                'active_subscribers_limit' => NULL,
                'trial_interval' => 'day',
            ),
            1 => 
            array (
                'id' => 8,
                'name' => 'Standard Month',
                'code' => 'STDM',
                'description' => 'Standard plan',
                'price' => '20.0000',
                'interval_unit' => 'year',
                'interval_count' => 1,
                'trial_period' => 0,
                'sort_order' => 1,
                'created_at' => '2021-03-21 13:56:47',
                'updated_at' => '2021-03-21 13:56:47',
                'is_active' => 1,
                'signup_fee' => '0',
                'currency' => 'GBP',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'grace_period' => 0,
                'grace_interval' => 'day',
                'prorate_day' => NULL,
                'prorate_period' => NULL,
                'prorate_extend_due' => NULL,
                'active_subscribers_limit' => NULL,
                'trial_interval' => 'day',
            ),
            2 => 
            array (
                'id' => 9,
                'name' => 'Pro Month',
                'code' => 'PROM',
                'description' => 'Pro plan',
                'price' => '25.0000',
                'interval_unit' => 'year',
                'interval_count' => 1,
                'trial_period' => 0,
                'sort_order' => 1,
                'created_at' => '2021-03-21 13:56:47',
                'updated_at' => '2021-03-21 13:56:47',
                'is_active' => 1,
                'signup_fee' => '0',
                'currency' => 'GBP',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'grace_period' => 0,
                'grace_interval' => 'day',
                'prorate_day' => NULL,
                'prorate_period' => NULL,
                'prorate_extend_due' => NULL,
                'active_subscribers_limit' => NULL,
                'trial_interval' => 'day',
            ),
            3 => 
            array (
                'id' => 10,
                'name' => 'Pro Year',
                'code' => 'PROY',
                'description' => 'Pro plan',
                'price' => '350.0000',
                'interval_unit' => 'year',
                'interval_count' => 1,
                'trial_period' => 0,
                'sort_order' => 1,
                'created_at' => '2021-03-21 13:56:47',
                'updated_at' => '2021-03-21 13:56:47',
                'is_active' => 1,
                'signup_fee' => '0',
                'currency' => 'GBP',
                'invoice_period' => 1,
                'invoice_interval' => 'year',
                'grace_period' => 0,
                'grace_interval' => 'day',
                'prorate_day' => NULL,
                'prorate_period' => NULL,
                'prorate_extend_due' => NULL,
                'active_subscribers_limit' => NULL,
                'trial_interval' => 'day',
            ),
            4 => 
            array (
                'id' => 11,
                'name' => 'Standard Month With Trial',
                'code' => 'STDMT',
                'description' => NULL,
                'price' => '12.9900',
                'interval_unit' => 'month',
                'interval_count' => 1,
                'trial_period' => 10,
                'sort_order' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'is_active' => 1,
                'signup_fee' => '0',
                'currency' => 'GBP',
                'invoice_period' => 1,
                'invoice_interval' => 'month',
                'grace_period' => 0,
                'grace_interval' => 'day',
                'prorate_day' => NULL,
                'prorate_period' => NULL,
                'prorate_extend_due' => NULL,
                'active_subscribers_limit' => NULL,
                'trial_interval' => 'day',
            ),
        ));
        
        
    }
}