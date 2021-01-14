<?php

use App\Models\Project;
use Illuminate\Database\Seeder;
 
class ProjectsTableSeeder extends Seeder
{
    public function run()
    {
 
        $faker = \Faker\Factory::create();
 
        // Create 50 product records
        for ($i = 0; $i < 50; $i++) {
            Project::create([
                'name' => $faker->title,
                'description' => $faker->paragraph,
            ]);
        }
    }
}
