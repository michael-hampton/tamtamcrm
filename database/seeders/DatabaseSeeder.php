<?php

namespace Database\Seeders;

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
        $this->call(leadColumns::class);
        $this->call(PaymentTermsSeeder::class);
        $this->call(DesignSeeder::class);
        $this->call(StatusTableSeeder::class);
        $this->call(IndustriesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(SourceTypeTableSeeder::class);
        $this->call(EventTypesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(PlansTableSeeder::class);
        $this->call(FeaturesTableSeeder::class);
        $this->call(PlanFeaturesTableSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);
        $this->call(PaymentGatewaysTableSeeder::class);
        $this->call(PaymentGatewayTypesTableSeeder::class);
    }

}
