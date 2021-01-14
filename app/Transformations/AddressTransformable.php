<?php

namespace App\Transformations;

use App\Models\Address;

class AddressTransformable
{

    /**
     * @param Address $address
     * @return Address
     */
    public function transformAddress(Address $address)
    {
        $obj = new Address;
        $obj->id = $address->id;
        $obj->alias = $address->alias;
        $obj->address_1 = $address->address_1;
        $obj->address_2 = $address->address_2;
        $obj->zip = $address->zip;
        $obj->city = $address->city;
        $obj->country_id = $address->country_id;
        $obj->address_type = $address->address_type;
        $obj->status = $address->status;
        return $obj;
    }

}
