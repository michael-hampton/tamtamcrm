<?php


namespace App\ViewModels;


use App\Models\CustomerContact;

class CustomerContactViewModel extends ViewModel
{
    /**
     * @var CustomerContact
     */
    private CustomerContact $customer_contact;

    /**
     * CustomerContactViewModel constructor.
     * @param CustomerContact $customer_contact
     */
    public function __construct(CustomerContact $customer_contact)
    {
        $this->customer_contact = $customer_contact;
    }

    /**
     * @return string
     */
    public function name()
    {
        if (!empty($this->customer_contact->first_name) && !empty($this->customer_contact->last_name)) {
            return $this->customer_contact->first_name . ' ' . $this->customer_contact->last_name;
        }

        return $this->customer_contact->customer->name ?: '';
    }
}