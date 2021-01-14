<?php

use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        \Illuminate\Support\Facades\DB::table('source_type')->insert([
            'name' => 'Personal Contact',
        ]);
        
        \Illuminate\Support\Facades\DB::table('source_type')->insert([
            'name' => 'Call',
        ]);
        
        \Illuminate\Support\Facades\DB::table('source_type')->insert([
            'name' => 'Email',
        ]);
        
        \Illuminate\Support\Facades\DB::table('source_type')->insert([
            'name' => 'Other',
        ]);
    }

}
