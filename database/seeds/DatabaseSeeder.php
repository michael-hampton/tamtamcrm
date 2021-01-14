<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(BankSeeder::class);
        die;
        $this->call(PaymentTermsSeeder::class);
        $this->call(EventTypeSeeder::class);
        $this->call(SourceTypeSeeder::class);
        $this->call(DesignSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(StatusTableSeeder::class);
        $this->call(GatewaySeeder::class);
        $this->call(RoleTableSeeder::class);
        (new Permissions())->create();
        $this->call(CurrenciesTableSeeder::class);
        $this->call(LanguagesSeeder::class);
        $this->call(IndustryTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
    }

}
