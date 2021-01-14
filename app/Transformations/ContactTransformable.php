<?php

namespace App\Transformations;

use App\Models\CustomerContact;

class ContactTransformable
{
    /**
     * @param CustomerContact $contact
     * @return array
     */
    public function transformContact(CustomerContact $contact)
    {
        return [
            'id'            => (int)$contact->id,
            'first_name'    => $contact->first_name ?: '',
            'last_name'     => $contact->last_name ?: '',
            'email'         => $contact->email ?: '',
            'is_primary'    => (bool)$contact->is_primary,
            'phone'         => $contact->phone ?: '',
            'custom_value1' => $contact->custom_value1 ?: '',
            'custom_value2' => $contact->custom_value2 ?: '',
            'custom_value3' => $contact->custom_value3 ?: '',
            'custom_value4' => $contact->custom_value4 ?: '',
            'password'      => ''
        ];
    }
}
