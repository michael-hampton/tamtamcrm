<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dev_role = Role::where('name', 'Admin')->first();
        $manager_role = Role::where('name', 'Manager')->first();

        $createTasks = new Permission();
        $createTasks->name = 'create-tasks';
        $createTasks->description = 'Create Tasks';
        $createTasks->save();
        $createTasks->roles()->attach($dev_role);

        $editUsers = new Permission();
        $editUsers->name = 'edit-users';
        $editUsers->description = 'Edit Users';
        $editUsers->save();
        $editUsers->roles()->attach($manager_role);
    }

}
