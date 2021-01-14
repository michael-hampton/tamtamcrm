<?php

namespace Tests\Unit;

use App\Factory\TaskFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Requests\SearchRequest;
use App\Search\TaskSearch;
use App\Transformations\TaskTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{

    use DatabaseTransactions, WithFaker, TaskTransformable;

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
    public function it_can_show_all_the_tasks()
    {
        $insertedtask = Task::factory()->create();
        $list = (new TaskSearch(
            new TaskRepository(
                new Task,
                new ProjectRepository(new Project)
            )
        ))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
        //$this->assertInstanceOf(Task::class, $list[0]);
        // $this->assertInstanceOf(Collection::class, $list);
        //$this->assertEquals($insertedtask->name, $myLastElement['name']);
    }

    /** @test */
    public function it_can_delete_the_task()
    {
        $task = Task::factory()->create();
        $deleted = $task->deleteEntity();
        $this->assertTrue($deleted);
    }

    public function it_can_archive_the_task()
    {
        $task = Task::factory()->create();
        $deleted = $task->archive();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_task()
    {
        $task = Task::factory()->create();
        $name = $this->faker->word;
        $data = ['name' => $name];
        $taskRepo = new TaskRepository($task, new ProjectRepository(new Project));
        $task = $taskRepo->updateTask($data, $task);
        $found = $taskRepo->findTaskById($task->id);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
    public function it_can_show_the_task()
    {
        $task = Task::factory()->create();
        $taskRepo = new TaskRepository(new Task, new ProjectRepository(new Project));
        $found = $taskRepo->findTaskById($task->id);
        $this->assertInstanceOf(Task::class, $found);
        $this->assertEquals($task->name, $found->name);
    }

    /** @test */
    public function it_can_attach_a_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $taskRepo = new TaskRepository($task, new ProjectRepository(new Project));
        $result = $taskRepo->syncUsers($task, [$user->id]);
        $this->assertArrayHasKey('attached', $result);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $data = [
            'account_id'   => $this->account->id,
            'task_type'    => 1,
            'task_status_id'  => 1,
            'customer_id'  => $this->customer->id,
            'name'         => $this->faker->word,
            'description'      => $this->faker->sentence,
            'is_completed' => 0,
            'due_date'     => $this->faker->dateTime,
        ];

        $taskRepo = new TaskRepository(new Task, new ProjectRepository(new Project));
        $factory = (new TaskFactory())->create($this->user, $this->account);
        $task = $taskRepo->createTask($data, $factory);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($data['name'], $task->name);
    }

    /** @test */
    public function it_can_create_a_project_task()
    {
        $project = Project::factory()->create();

        $data = [
            'project_id'   => $project->id,
            'account_id'   => $this->account->id,
            'task_type'    => 1,
            'task_status_id'  => 1,
            'customer_id'  => $this->customer->id,
            'name'         => $this->faker->word,
            'description'      => $this->faker->sentence,
            'is_completed' => 0,
            'due_date'     => $this->faker->dateTime,
        ];

        $taskRepo = new TaskRepository(new Task, new ProjectRepository(new Project));
        $factory = (new TaskFactory())->create($this->user, $this->account);
        $task = $taskRepo->createTask($data, $factory);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($data['name'], $task->name);
        $this->assertEquals($data['project_id'], $task->project_id);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_task_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $task = new TaskRepository(new Task, new ProjectRepository(new Project));
        $task->createTask([]);
    }

    /** @test */
    public function it_errors_finding_a_task()
    {
        $this->expectException(ModelNotFoundException::class);
        $task = new TaskRepository(new Task, new ProjectRepository(new Project));
        $task->findTaskById(999);
    }

    /** @test */
    public function it_can_transform_task()
    {
        $name = $this->faker->name;
        $description = $this->faker->sentence;
        $due_date = $this->faker->dateTime;
        $task_type = 2;

        $address = Task::factory()->create(
            [
                'account_id' => $this->account->id,
                'name'       => $name,
                'description'    => $description,
                'due_date'   => $due_date
            ]
        );

        $transformed = $this->transformTask($address);
        $this->assertNotEmpty($transformed);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
