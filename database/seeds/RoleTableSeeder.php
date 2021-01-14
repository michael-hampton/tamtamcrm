<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $dev_permission = Permission::where('name', 'create-tasks')->first();
//        $manager_permission = Permission::where('name', 'edit-users')->first();

        $account = \App\Models\Account::first();
        $user = \App\Models\User::first();

        $arrRoles = [
            0 => [
                'id'           => 3,
                'name'         => 'Admin',
                'display_name' => 'Admin',
                'description'  => 'Admin',
                'account_id'   => $account->id,
                'user_id'      => $user->id
            ],
            1 => [
                'id'           => 4,
                'name'         => 'Manager',
                'display_name' => 'Manager',
                'description'  => 'Manager',
                'account_id'   => $account->id,
                'user_id'      => $user->id
            ]
        ];

        foreach ($arrRoles as $arr_role) {
            Role::create($arr_role);
            $user->roles()->attach($arr_role['id']);
        }
    }

}
