<?php

use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('event_types')->insert([
            'name' => 'Event',
        ]);

        DB::table('event_types')->insert([
            'name' => 'Call',
        ]);

        DB::table('event_types')->insert([
            'name' => 'Meeting',
        ]);

        DB::table('event_types')->insert([
            'name' => 'Task',
        ]);
    }

}
