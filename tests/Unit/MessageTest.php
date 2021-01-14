<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use App\Repositories\MessageRepository;
use App\Transformations\MessageUserTransformable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MessageTest extends TestCase
{

    use DatabaseTransactions, MessageUserTransformable, WithFaker;

    private $customer;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->customer = Customer::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_delete_a_message()
    {
        $message = Message::factory()->create();
        $messageRepo = new MessageRepository($message);
        $delete = $messageRepo->deleteMessage();
        $this->assertTrue($delete);
    }

    /** @test */
    public function it_can_create_a_message()
    {
        $data = [
            'user_id'     => $this->user->id,
            'customer_id' => $this->customer->id,
            'message'     => $this->faker->sentence,
            'has_seen'    => 1,
            'direction'   => 1
        ];

        $message = new MessageRepository(new Message);
        $created = $message->createMessage($data);
        $this->assertInstanceOf(Message::class, $created);
        $this->assertEquals($data['user_id'], $created->user_id);
        $this->assertEquals($data['customer_id'], $created->customer_id);
        $this->assertEquals($data['message'], $created->message);
        $collection = collect($data);
        $this->assertDatabaseHas('messages', $collection->all());
    }

    public function it_errors_creating_the_message_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $messageRepo = new MessageRepository(new Message);
        $messageRepo->createMessage([]);
    }

    /** @test */
    public function it_can_list_all_messages()
    {
        $data = [
            'user_id'     => $this->user->id,
            'customer_id' => $this->customer->id,
            'message'     => $this->faker->sentence,
            'has_seen'    => 1,
            'direction'   => 1
        ];

        $messageRepo = new MessageRepository(new Message);
        $messageRepo->createMessage($data);
        $list = $messageRepo->getMessagesForCustomer($this->customer, $this->user);
        $this->assertInstanceOf(Collection::class, $list);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        //$this->user = null;
        //$this->customer = null;
    }

}
