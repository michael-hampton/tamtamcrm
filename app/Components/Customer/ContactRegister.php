<?php

namespace App\Components\Customer;

use App\Factory\CustomerContactFactory;
use App\Factory\CustomerFactory;
use App\Models\Account;
use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ContactRegister
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var User
     */
    private User $user;

    /**
     * ContactRegister constructor.
     * @param array $data
     * @param Account $account
     * @param User $user
     */
    public function __construct(array $data, Account $account, User $user)
    {
        $this->data = $data;
        $this->account = $account;
        $this->user = $user;
    }

    /**
     * @return CustomerContact
     */
    public function create()
    {
        $customer = $this->createCustomer();
        $this->createAddress($customer);
        $this->createShippingAddress($customer);
        $contact = $this->createContact($customer);

        return $contact;
    }

    /**
     * @return Customer
     */
    private function createCustomer(): Customer
    {
        $customer = CustomerFactory::create($this->account, $this->user);
        $customer->website = !empty($this->data['website']) ? $this->data['website'] : '';
        $customer->name = !empty($this->data['name']) ? $this->data['name'] : '';
        $customer->account_id = $this->account->id;
        $customer->save();

        return $customer;
    }

    /**
     * @param Customer $customer
     */
    private function createAddress(Customer $customer)
    {
        $address = new Address();
        $address->address_type = 1;
        $address->customer_id = $customer->id;
        $address->fill($this->data);
        $address->save();
    }

    /**
     * @param Customer $customer
     * @return bool
     */
    private function createShippingAddress(Customer $customer)
    {
        if (empty($this->data['shipping_address1'])) {
            return true;
        }

        $address = new Address();
        $address->address_type = 2;
        $address->customer_id = $customer->id;
        $address->address_1 = $this->data['shipping_address1'];
        $address->address_2 = $this->data['shipping_address2'];
        $address->city = $this->data['shipping_city'];
        $address->state_code = $this->data['shipping_state'];
        $address->zip = $this->data['shipping_postal_code'];
        $address->country_id = $this->data['shipping_country'];
        $address->save();
    }

    /**
     * @param Customer $customer
     * @return CustomerContact
     */
    private function createContact(Customer $customer)
    {
        $client_contact = CustomerContactFactory::create($this->account, $this->user, $customer);
        $client_contact->fill($this->data);

        $client_contact->customer_id = $customer->id;
        $client_contact->is_primary = true;
        $client_contact->password = Hash::make($this->data['password']);

        $client_contact->save();

        return $client_contact;
    }
}
