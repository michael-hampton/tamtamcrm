<?php

use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(\App\Models\Task::class, 400)->create();
    }

}
