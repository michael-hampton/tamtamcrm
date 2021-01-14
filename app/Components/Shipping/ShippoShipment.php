<?php

namespace App\Components\Shipping;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Shippo;
use Shippo_Get_Shipping_Rates;
use Shippo_Shipment;
use Shippo_Transaction;

class ShippoShipment
{
    /**
     * @var Customer
     */
    private Customer $customer;

    /**
     * @var array
     */
    private array $rates = [];

    /**
     * @var Order
     */
    private Order $order;

    /**
     * The address where to pick up the item for delivery
     *
     * @var $warehouseAddress
     */
    private $warehouseAddress;

    /**
     * The address of the customer where the item is to be delivered
     *
     * @var $deliveryAddress
     */
    private $deliveryAddress;

    /**
     * The item/s
     *
     * @var $parcel
     */
    private $parcel;

    private $line_items;

    /**
     * ShippoShipment constructor.
     * @param Customer $customer
     * @param $line_items
     */
    public function __construct(Customer $customer, $line_items)
    {
        Shippo::setApiKey(env('SHIPPO_PRIVATE'));
        $this->customer = $customer;
        $this->line_items = $line_items;
    }

    public function createShippingProcess()
    {
        if ($this->customer->addresses()->count() > 0 && count($this->line_items) > 0) {
            $this->setPickupAddress();
            $deliveryAddress = $this->customer->addresses->where('address_type', '=', 2)->first();

            $this->setDeliveryAddress($deliveryAddress);
            $this->readyParcel($this->line_items);

            return $this->readyShipment();
        }
    }

    /**
     * Address where the shipment will be picked up
     */
    private function setPickupAddress()
    {
        $warehouse = array(
            'name'    => 'Shawn Ippotle',
            'street1' => '215 Clayton St.',
            'city'    => 'San Francisco',
            'state'   => 'CA',
            'zip'     => '94117',
            'country' => 'US',
            'phone'   => $this->customer->account->settings->phone,
            'email'   => $this->customer->account->settings->email
        );

//        $warehouse = [
//            'name'    => $this->customer->account->settings->name,
//            'street1' => $this->customer->account->settings->address1,
//            'city'    => $this->customer->account->settings->city,
//            'state'   => $this->customer->account->settings->state,
//            'zip'     => $this->customer->account->settings->postal_code,
//            'country' => $this->customer->account->country()->iso,
//            'phone'   => $this->customer->account->settings->phone,
//            'email'   => $this->customer->account->settings->email
//        ];

        $this->warehouseAddress = $warehouse;
    }

    /**
     * @param Address $address
     */
    private function setDeliveryAddress(Address $address)
    {
        $contact = $this->customer->primary_contact()->first();

        $delivery = [
            'name'    => $this->customer->name,
            'street1' => $address->address_1,
            'city'    => $address->city,
            'state'   => $address->state_code,
            'zip'     => $address->zip,
            'country' => $address->country->iso,
            'phone'   => !empty($contact) ? $contact->phone : '',
            'email'   => $this->customer->email
        ];

        $this->deliveryAddress = $delivery;
    }

    /**
     * @param array $line_items
     * @return void
     */
    private function readyParcel(array $line_items)
    {
        $weight = collect($line_items)->map(
            function ($item) {
                $product = Product::find($item['product_id']);

                return [
                    'weight'    => $product->weight * $item['quantity'],
                    'mass_unit' => $product->mass_unit
                ];
            }
        )->map(
            function ($item) {
                $total = 0;
                switch ($item['mass_unit']) {
                    case Product::MASS_UNIT['OUNCES'] :
                        $oz = $item['weight'] / 16;
                        $total += $oz;
                        break;
                    case Product::MASS_UNIT['GRAMS'] :
                        $oz = $item['weight'] * 0.0022;
                        $total += $oz;
                        break;
                    default:
                        $total += $item['weight'];
                }
                return [
                    'weight' => $total
                ];
            }
        )->sum('weight');

        $parcel = array(
            'length'        => '5',
            'width'         => '5',
            'height'        => '5',
            'distance_unit' => 'in',
            'weight'        => $weight,
            'mass_unit'     => 'lb',
        );

        $this->parcel = $parcel;
    }

    /**
     * @return Shippo_Shipment
     */
    private function readyShipment()
    {
        $shipment = Shippo_Shipment::create(
            array(
                'address_from' => $this->warehouseAddress,
                'address_to'   => $this->deliveryAddress,
                'parcels'      => $this->parcel,
                'async'        => false
            )
        );

        foreach ($shipment['rates'] as $key => $rate) {
            $this->rates[$key]['amount'] = $rate->amount;
            $this->rates[$key]['name'] = $rate->provider;
            $this->rates[$key]['object_id'] = $rate->object_id;
        }

        return $shipment;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function createLabel(Order $order)
    {
        $transaction = Shippo_Transaction::create(
            array(
                'rate'            => $order->shipping_id,
                'label_file_type' => "PDF",
                'async'           => false
            )
        );

        if ($transaction["status"] == "SUCCESS" && !empty($transaction["label_url"])) {
            $order->shipping_label_url = $transaction["label_url"];
            $order->save();

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
//    public function getRates(string $id, string $currency = 'USD')
//    {
//        return Shippo_Shipment::get_shipping_rates(compact('id', 'currency'));
//    }

    public function getRates()
    {
        return $this->rates;
    }
}
