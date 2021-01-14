<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $name = $this->faker->unique()->randomElement(
            [
                'Gear',
                'Clothing',
                'Shoes',
                'Diapering',
                'Feeding',
                'Bath',
                'Toys',
                'Nursery',
                'Household',
                'Grocery'
            ]
        );
        //$file = UploadedFile::fake()->image('category.png', 600, 600);
        return [
            'user_id'     => $user->id,
            'account_id'  => 1,
            'name'        => $name,
            'slug'        => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->paragraph,
            //'cover' => $file->store('categories', ['disk' => 'public']),
            'status'      => 1
        ];
    }
}
