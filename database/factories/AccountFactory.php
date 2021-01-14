<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $domain = \App\Models\Domain::first();
        $settings = (new \App\Settings\AccountSettings)->getAccountDefaults();

        $settings->phone = $this->faker->phoneNumber;
        $settings->website = $this->faker->url;
        $settings->address1 = $this->faker->address;
        $settings->city = $this->faker->city;
        $settings->email = $this->faker->email;
        $settings->inclusive_taxes = false;

        return [
            'domain_id'     => $domain->id,
            'subdomain'     => 'loans-website.develop',
            'support_email' => $this->faker->email,
            'ip'            => $this->faker->ipv4,
            'settings'      => $settings,
            'custom_fields' => (object)['custom1' => '1', 'custom2' => '2', 'custom3' => '3']
        ];
    }
}
