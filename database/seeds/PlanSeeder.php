<?php

use App\Models\Plan;

class PlanSeeder extends \Illuminate\Database\Seeder
{

    public function run()
    {

        $plan = Plan::create(
            [
                'name'             => 'Standard Year',
                'description'      => 'Standard plan',
                'price'            => 200,
                'signup_fee'       => 0,
                'invoice_period'   => 1,
                'invoice_interval' => 'year',
                'trial_period'     => 0,
                'trial_interval'   => 'day',
                'sort_order'       => 1,
                'currency'         => 'GBP',
                'code'             => 'STDY'
            ]
        );

        $plan = Plan::create(
            [
                'name'             => 'Standard Month',
                'description'      => 'Standard plan',
                'price'            => 20,
                'signup_fee'       => 0,
                'invoice_period'   => 1,
                'invoice_interval' => 'month',
                'trial_period'     => 0,
                'trial_interval'   => 'day',
                'sort_order'       => 1,
                'currency'         => 'GBP',
                'code'             => 'STDM'
            ]
        );

        $plan = Plan::create(
            [
                'name'             => 'Pro Month',
                'description'      => 'Pro plan',
                'price'            => 25,
                'signup_fee'       => 0,
                'invoice_period'   => 1,
                'invoice_interval' => 'month',
                'trial_period'     => 0,
                'trial_interval'   => 'day',
                'sort_order'       => 1,
                'currency'         => 'GBP',
                'code'             => 'PROM'
            ]
        );

        $plan = Plan::create(
            [
                'name'             => 'Pro Year',
                'description'      => 'Pro plan',
                'price'            => 350,
                'signup_fee'       => 0,
                'invoice_period'   => 1,
                'invoice_interval' => 'year',
                'trial_period'     => 0,
                'trial_interval'   => 'day',
                'sort_order'       => 1,
                'currency'         => 'GBP',
                'code'             => 'PROY'
            ]
        );
    }
}