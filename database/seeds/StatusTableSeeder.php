<?php

use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
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
                'name'        => 'TODO',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-bars',
                'is_active'   => 1,
                'task_type'   => 1,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Blocked',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-lightbulb',
                'is_active'   => 1,
                'task_type'   => 1,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'In Progress',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-spinner',
                'is_active'   => 1,
                'task_type'   => 1,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Done',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 1,
                'account_id'  => 1,
            ]
        );

        // deals
        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Prospecting',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Qualification',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Needs Analysis',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Value Propsition',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Identified Decision Makers',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Perception Analysis',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Proposal Price\Quote',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Closed Won',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );

        \Illuminate\Support\Facades\DB::table('task_statuses')->insert(
            [
                'name'        => 'Closed Lost',
                'description' => 'You can do what you want to do with this column',
                'icon'        => 'fa-check',
                'is_active'   => 1,
                'task_type'   => 2,
                'account_id'  => 1,
            ]
        );
    }

}
