<?php

use Illuminate\Database\Seeder;

class leadColumns extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Unassigned',
                'description' => 'Description 1',
                'icon'        => 'fa-bars',
                'is_active'   => 1,
                'task_type'   => 3,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Partner Leads',
                'description' => 'Description 2',
                'icon'        => 'fa-lightbulb',
                'is_active'   => 1,
                'task_type'   => 3,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Responsible Assigned',
                'description' => 'Description 3',
                'icon'        => 'fa-spinner',
                'is_active'   => 1,
                'task_type'   => 3,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Waiting For Details',
                'description' => 'Description 4',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 3,
                'account_id'  => 1,
            ]
        );
    }

}
