<?php

use Illuminate\Database\Seeder;

class PaymentTermsSeeder extends Seeder
{
    public function run()
    {
        $user = \App\Models\User::first();

        Eloquent::unguard();
        $paymentTerms = [
            ['name' => 'Immediate', 'number_of_days' => 1, 'account_id' => 1, 'user_id' => $user->id],
            ['name' => 'Net 30', 'number_of_days' => 30, 'account_id' => 1, 'user_id' => $user->id],
            ['name' => 'Net 45', 'number_of_days' => 45, 'account_id' => 1, 'user_id' => $user->id],
            ['name' => 'Net 60', 'number_of_days' => 60, 'account_id' => 1, 'user_id' => $user->id],

        ];
        foreach ($paymentTerms as $paymentTerm) {
            $record = \App\Models\PaymentTerms::where('name', '=', $paymentTerm['name'])->first();
            if ($record) {
                $record->name = $paymentTerm['name'];
                $record->gateway_type_id = !empty($paymentType['gateway_type_id']) ? $paymentType['gateway_type_id'] : null;
                $record->save();
            } else {
                \App\Models\PaymentTerms::create($paymentTerm);
            }
        }
    }
}
