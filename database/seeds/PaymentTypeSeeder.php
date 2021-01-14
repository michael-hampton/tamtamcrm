<?php
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;

class PaymentTypeSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        $paymentTypes = [
            ['name' => 'Apply Credit'],
            ['name' => 'Bank Transfer', 'gateway_type_id' => 2],
            ['name' => 'Cash'],
            ['name' => 'Debit', 'gateway_type_id' => 1],
            ['name' => 'PayPal', 'gateway_type_id' => 3],
            ['name' => 'Check'],
        ];
        foreach ($paymentTypes as $paymentType) {
            $record = \App\Models\PaymentMethod::where('name', '=', $paymentType['name'])->first();
            if ($record) {
                $record->name = $paymentType['name'];
                $record->gateway_type_id = !empty($paymentType['gateway_type_id']) ? $paymentType['gateway_type_id'] : null;
                $record->save();
            } else {
                \App\Models\PaymentMethod::create($paymentType);
            }
        }
    }
}
