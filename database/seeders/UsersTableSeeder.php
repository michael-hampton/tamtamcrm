<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dev_role = Role::where('name', 'developer')->first();
        $manager_role = Role::where('name', 'manager')->first();
        $dev_perm = Permission::where('name', 'create-tasks')->first();
        $manager_perm = Permission::where('name', 'edit-users')->first();

        $developer = new User();
        $developer->first_name = 'Usama';
        $developer->last_name = 'Muneer';
        $developer->username = 'usama.muneer';
        $developer->email = 'usama@thewebtier.com';
        $developer->password = \Illuminate\Support\Facades\Hash::make('secret');
        $developer->save();
        $developer->roles()->attach($dev_role);
        $developer->permissions()->attach($dev_perm);
    }

}
