<?php

namespace Tests\Unit;

use App\Factory\EventFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use App\Repositories\EventRepository;
use App\Transformations\EventTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{

    use DatabaseTransactions, EventTransformable, WithFaker;

    /**
     * @var int
     */
    private $account;

    private $user;

    private $customer;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->account = Account::where('id', 1)->first();
    }

    /** @test */
    public function it_can_transform_the_event()
    {
        $event = Event::factory()->create();
        $repo = new EventRepository($event);
        $eventFromDb = $repo->findEventById($event->id);
        $cust = $this->transformEvent($event);
        //$this->assertInternalType('string', $eventFromDb->status);
        $this->assertNotEmpty($cust);
    }

    /** @test */
    public function it_can_delete_a_event()
    {
        $event = Event::factory()->create();
        $delete = $event->deleteEntity();
        $this->assertTrue($delete);
        //$this->assertDatabaseHas('events', $event->toArray());
    }

    /** @test */
    public function it_fails_when_the_event_is_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $event = new EventRepository(new Event);
        $event->findEventById(999);
    }

    /** @test */
    public function it_can_find_a_event()
    {
        $data = [
            'account_id'  => $this->account->id,
            'customer_id' => $this->customer->id,
            'title'       => $this->faker->sentence,
            'location'    => $this->faker->sentence,
            'beginDate'   => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'endDate'     => $this->faker->dateTime()->format('Y-m-d H:i:s'),
        ];


        $eventRepo = new EventRepository(new Event);
        $eventFactory = (new EventFactory())->create($this->user, $this->account);
        $created = $eventRepo->save($data, $eventFactory);
        $found = $eventRepo->findEventById($created->id);
        $this->assertInstanceOf(Event::class, $found);
        $this->assertEquals($data['title'], $found->title);
        $this->assertEquals($data['location'], $found->location);
    }

    /** @test */
    public function it_can_update_the_event()
    {
        $cust = Event::factory()->create();
        $event = new EventRepository($cust);
        $update = [
            'location' => $this->faker->sentence,
        ];
        $updated = $event->save($update, $cust);
        $this->assertInstanceOf(Event::class, $updated);
        $this->assertEquals($update['location'], $cust->location);
        $this->assertDatabaseHas('events', $update);
    }

    /** @test */
    public function it_can_create_a_event()
    {
        $factory = (new EventFactory())->create($this->user, $this->account);
        $data = [
            'account_id'  => $this->account->id,
            'title'       => $this->faker->word,
            'description' => $this->faker->sentence,
            'location'    => $this->faker->sentence,
            'beginDate'   => $this->faker->dateTime(),
            'endDate'     => $this->faker->dateTime(),
            'customer_id' => $this->customer->id,
            'event_type'  => 2
        ];

        $event = new EventRepository(new Event);
        $created = $event->save($data, $factory);
        $this->assertInstanceOf(Event::class, $created);
        $this->assertEquals($data['title'], $created->title);
        $this->assertEquals($data['location'], $created->location);
        $collection = collect($data);
        $this->assertDatabaseHas('events', $collection->all());
    }

    public function it_errors_creating_the_event_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $task = new EventRepository(new Event);
        $task->createEvent([]);
    }

    /** @test */
    public function it_can_attach_a_task()
    {
        $task = Task::factory()->create();
        $event = Event::factory()->create();
        $eventRepo = new EventRepository($event);
        $result = $eventRepo->syncTask($event, $task->id);
        $this->assertArrayHasKey('attached', $result);
    }

    /** @test */
    public function it_can_attach_a_user()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $eventRepo = new EventRepository($event);
        $result = $eventRepo->attachUsers($event, [$user->id]);
        $this->assertTrue($result);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
