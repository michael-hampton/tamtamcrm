<?php


namespace App\Http\Controllers;


use App\Components\Shipping\ShippoShipment;
use App\Models\Customer;
use Illuminate\Http\Request;

class ShippingController
{

    public function getRates(Request $request)
    {
        $customer = Customer::find($request->input('customer_id'));

        $objShipping = new ShippoShipment($customer, $request->input('products'));

        $objShipping->createShippingProcess();

        return response()->json(['rates' => $objShipping->getRates()]);
    }
}