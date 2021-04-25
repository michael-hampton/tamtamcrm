<?php

namespace App\Repositories;

use App\Factory\CustomerContactFactory;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Hash;

/**
 * CustomerContactRepository
 */
class CustomerContactRepository extends BaseRepository
{
    /**
     * CustomerContactRepository constructor.
     * @param CustomerContact $customer_contact
     */
    public function __construct(CustomerContact $customer_contact)
    {
        parent::__construct($customer_contact);
        $this->model = $customer_contact;
    }

    /**
     * @param array $data
     * @param CustomerContact $customer_contact
     * @return CustomerContact
     */
    public function createContact(array $data, CustomerContact $customer_contact)
    {
        $customer_contact->fill($data);

        $customer_contact->save();

        return $customer_contact;
    }

    /**
     * @param array $contacts
     * @param Customer $customer
     * @return bool
     */
    public function save(array $contacts, Customer $customer): bool
    {
        // assign temporary ids if missing
        foreach ($contacts as $key => $contact) {
            if (empty($contact['id'])) {
                $contacts[$key]['id'] = uniqid();
            }
        }

        $contacts_obs = CustomerContact::whereCustomerId($customer->id)->get()->keyBy('id');

        $array_database = $contacts_obs->toArray();
        $array_client = collect($contacts)->keyBy('id')->toArray();

        //update
        foreach (array_intersect_key($array_database, $array_client) as $id => $data) {
            $data = $array_client[$id];
            $contact = $contacts_obs[$id];
            $contact->fill($data);
            $contact->password = isset($update['password']) && strlen($update['password']) > 0 ? Hash::make(
                $update['password']
            ) : $contact->password;

            $contact->save();
        }

        // insert
        foreach (array_diff_key($array_client, $array_database) as $id => $data) {
            $data = $array_client[$id];
            unset($data['id']);
            $create_contact = CustomerContactFactory::create($customer->account, $customer->user, $customer);
            $create_contact->customer_id = $customer->id;
            $create_contact->fill($data);
            $create_contact->password = isset($item['password']) && strlen($item['password']) > 0 ? Hash::make(
                $item['password']
            ) : '';

            $create_contact->save();
        }

        // delete
        foreach (array_diff_key($array_database, $array_client) as $id => $data) {
            $contact = $contacts_obs[$id];
            $contact->delete();
        }

        return true;
    }
}
