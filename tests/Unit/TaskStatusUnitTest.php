<?php

namespace Tests\Unit;

use App\Factory\TaskStatusFactory;
use App\Models\Account;
use App\Models\TaskStatus;
use App\Models\User;
use App\Repositories\TaskStatusRepository;
use App\Requests\SearchRequest;
use App\Search\TaskStatusSearch;
use App\Shop\Orders\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskStatusUnitTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private Account $account;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $this->user = User::factory()->create();
        $this->account = Account::where('id', 1)->first();
    }

    /** @test */
    public function it_can_delete_the_order_status()
    {
        $os = TaskStatus::factory()->create();
        $os->deleteEntity();
        $this->assertDatabaseMissing('task_statuses', $os->toArray());
    }

    /** @test */
    public function it_lists_all_the_task_statuses()
    {
        TaskStatus::factory()->create();
        $list = (new TaskStatusSearch(new TaskStatusRepository(new TaskStatus())))->filter(
            new SearchRequest(),
            $this->account
        );
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_errors_getting_not_existing_order_status()
    {
        $this->expectException(ModelNotFoundException::class);
        $taskStatusRepo = new TaskStatusRepository(new TaskStatus);
        $taskStatusRepo->findTaskStatusById(999);
    }

    /** @test */
    public function it_can_get_the_task_status()
    {
        $create = [
            'name'         => $this->faker->name,
            'column_color' => $this->faker->word
        ];
        $taskStatusRepo = new TaskStatusRepository(new TaskStatus);
        $taskStatus = TaskStatusFactory::create($this->account, $this->user);
        $os = (new TaskStatusRepository(new TaskStatus()))->save($create, $taskStatus);
        $this->assertEquals($create['name'], $os->name);
        $this->assertEquals($create['column_color'], $os->column_color);
    }

    /** @test */
    public function it_can_update_the_task_status()
    {
        $os = TaskStatus::factory()->create();
        $taskStatusRepo = new TaskStatusRepository($os);
        $data = [
            'name'         => $this->faker->name,
            'column_color' => $this->faker->word
        ];
        $updated = $taskStatusRepo->save($data, $os);
        $this->assertInstanceOf(TaskStatus::class, $updated);
        $found = $taskStatusRepo->findTaskStatusById($os->id);
        $this->assertEquals($data['name'], $found->name);
        $this->assertEquals($data['column_color'], $found->column_color);
    }

    /** @test */
    public function it_can_create_the_task_status()
    {
        $create = [
            'name'         => $this->faker->name,
            'task_type'    => 1,
            'description'  => $this->faker->sentence,
            'column_color' => $this->faker->word
        ];
        $taskStatusRepo = new TaskStatusRepository(new TaskStatus);
        $taskStatus = TaskStatusFactory::create($this->account, $this->user);
        $taskStatus = (new TaskStatusRepository(new TaskStatus()))->save($create, $taskStatus);
        $this->assertEquals($create['name'], $taskStatus->name);
        $this->assertEquals($create['column_color'], $taskStatus->column_color);
    }

    /** @test */
    public function it_errors_creating_the_task_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $task = new TaskStatusRepository(new TaskStatus);
        $task->createTaskStatus([]);
    }
}
