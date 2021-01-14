<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = $this->faker->unique()->sentence;
        $user = User::factory()->create();
        $company = Company::factory()->create();

        return [
            'company_id'    => $company->id,
            'account_id'    => 1,
            'user_id'       => $user->id,
            'quantity'      => 0,
            'sku'           => $this->faker->numberBetween(1111111, 999999),
            'name'          => $product,
            'slug'          => \Illuminate\Support\Str::slug($product),
            'description'   => $this->faker->paragraph,
            'price'         => $this->faker->randomNumber(2),
            'status'        => 1,
            'length'        => 5,
            'width'         => 10,
            'height'        => 15,
            'weight'        => 20,
            'mass_unit'     => 'gms',
            'distance_unit' => 'cm'
        ];
    }
}
