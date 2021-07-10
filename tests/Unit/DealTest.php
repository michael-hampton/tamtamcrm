<?php

namespace Tests\Unit;

use App\Factory\DealFactory;
use App\Models\Account;
use App\Models\Cases;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Repositories\DealRepository;
use App\Requests\SearchRequest;
use App\Search\DealSearch;
use App\Transformations\DealTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DealTest extends TestCase
{

    use DatabaseTransactions, WithFaker, DealTransformable;

    private $user;
    private $customer;
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
    }

    /** @test */
    public function it_can_show_all_the_deals()
    {
        Deal::factory()->create();

        $list = (new DealSearch(
            new DealRepository(
                new Deal
            )
        ))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
        //$this->assertInstanceOf(Deal::class, $list[0]);
        // $this->assertInstanceOf(Collection::class, $list);
        //$this->assertEquals($insertedtask->name, $myLastElement['name']);
    }

    /** @test */
    public function it_can_delete_the_deal()
    {
        $deal = Deal::factory()->create();
        $deleted = $deal->deleteEntity();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_archive_the_deal()
    {
        $deal = Deal::factory()->create();
        $deleted = $deal->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_deal()
    {
        $deal = Deal::factory()->create();
        $name = $this->faker->word;
        $data = ['name' => $name];
        $dealRepo = new DealRepository($deal);
        $deal = $dealRepo->update($data, $deal);
        $found = $dealRepo->findDealById($deal->id);
        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
    public function it_can_show_the_deal()
    {
        $deal = Deal::factory()->create();
        $dealRepo = new DealRepository(new Deal);
        $found = $dealRepo->findDealById($deal->id);
        $this->assertInstanceOf(Deal::class, $found);
        $this->assertEquals($deal->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_deal()
    {
        $data = [
            'account_id'   => $this->account->id,
            'task_status'  => 1,
            'customer_id'  => $this->customer->id,
            'name'        => $this->faker->word,
            'description'  => $this->faker->sentence,
            'is_completed' => 0,
            'due_date'     => $this->faker->dateTime,
        ];

        $order_id = Deal::max('order_id') + 1;
        $task_status = TaskStatus::ByTaskType(2)->orderBy('order_id', 'asc')->first();

        $dealRepo = new DealRepository(new Deal);
        $factory = (new DealFactory())->create($this->user, $this->account);
        $deal = $dealRepo->create($data, $factory);

        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertEquals($deal->task_status_id, $task_status->id);
        $this->assertEquals($deal->order_id, $order_id);
        $this->assertEquals($data['name'], $deal->name);
    }


    /** @test */
    public function it_errors_finding_a_deal()
    {
        $this->expectException(ModelNotFoundException::class);
        $task = new DealRepository(new Deal);
        $task->findDealById(999);
    }

    /** @test */
    public function it_can_transform_task()
    {
        $name = $this->faker->name;
        $description = $this->faker->sentence;
        $due_date = $this->faker->dateTime;
        $task_type = 2;

        $address = Deal::factory()->create(
            [
                'account_id'  => $this->account->id,
                'name'       => $name,
                'description' => $description,
                'due_date'    => $due_date

            ]
        );

        $transformed = $this->transformDeal($address);
        $this->assertNotEmpty($transformed);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
