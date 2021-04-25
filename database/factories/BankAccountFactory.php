<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $bank = Bank::first();
        $account = \App\Models\Account::first();

        return [
            'user_id'       => $user->id,
            'account_id'    => $account->id,
            'bank_id'       => $bank->id,
            'username'      => $this->faker->userName,
            'password'      => $this->faker->password,
            'public_notes'  => $this->faker->paragraph,
            'private_notes' => $this->faker->paragraph
        ];
    }
}
