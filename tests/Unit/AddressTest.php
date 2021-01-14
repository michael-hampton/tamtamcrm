<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\Customer;
use App\Repositories\AddressRepository;
use App\Repositories\CustomerRepository;
use App\Transformations\AddressTransformable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_can_delete_the_address()
    {
        $created = Address::factory()->create();
        $address = new AddressRepository($created);
        $delete = $address->deleteAddress();
        $this->assertTrue($delete);
        //$this->assertDatabaseHas('addresses', ['id' => $created->id]);
    }

    /** @test */
    public function it_can_transform_address()
    {
        $customer = Customer::factory()->create();

        $city = $this->faker->city;
        $country = 225;

        $address = Address::factory()->create(
            [
                'city'        => $city,
                'country_id'  => $country,
                'customer_id' => $customer->id,
            ]
        );

        $transformed = (new AddressTransformable())->transformAddress($address);

        $this->assertEquals($city, $transformed->city);
        $this->assertEquals($country, $transformed->country_id);
    }

    /** @test */
    public function it_can_update_the_address()
    {
        $address = Address::factory()->create();
        $data = [
            'alias'     => $this->faker->title('Male'),
            'address_1' => $this->faker->streetName,
            'address_2' => $this->faker->streetAddress,
            'zip'       => $this->faker->postcode,
            'status'    => 1
        ];

        $addressRepo = new AddressRepository($address);
        $updated = $addressRepo->updateAddress($data);
        $address = $addressRepo->findAddressById($address->id);
        $this->assertTrue($updated);
        $this->assertEquals($data['alias'], $address->alias);
        $this->assertEquals($data['address_1'], $address->address_1);
        $this->assertEquals($data['address_2'], $address->address_2);
        $this->assertEquals($data['zip'], $address->zip);
        $this->assertEquals($data['status'], $address->status);
    }

    /** @test */
    public function it_can_return_the_owner_of_the_address()
    {
        $customer = Customer::factory()->create();
        $address = Address::factory()->create(['customer_id' => $customer->id]);
        $addressRepo = new AddressRepository($address);
        $found = $addressRepo->findCustomer();
        $this->assertEquals($customer->name, $found->name);
    }

    /** @test */
    public function it_can_be_attached_to_a_customer()
    {
        $customer = Customer::factory()->create();
        $address = Address::factory()->create();
        $addressRepo = new AddressRepository($address);
        $addressRepo->attachToCustomer($address, $customer);
        $this->assertEquals($customer->name, $address->customer->name);
    }

    /** @test */
    public function it_can_list_all_the_addresses()
    {
        $address = Address::factory()->create();
        $address = new AddressRepository($address);
        $addresses = $address->listAddress();
        foreach ($addresses as $list) {
            $this->assertDatabaseHas('addresses', ['alias' => $list->alias]);
        }
    }

    /** @test */
    public function it_can_show_the_address()
    {
        $address = Address::factory()->create();
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }

    /** @test */
    public function it_can_list_all_the_addresses_of_the_customer()
    {
        $customer = Customer::factory()->create();
        Address::factory()->create(['customer_id' => $customer->id]);
        $customerRepo = new CustomerRepository($customer);
        $lists = $customerRepo->findAddresses();
        $this->assertCount(1, $lists);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
