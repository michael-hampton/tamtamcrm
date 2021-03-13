<?php

namespace Tests\Unit;

use App\Factory\ProjectFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Requests\SearchRequest;
use App\Search\ProjectSearch;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

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
        $this->account = Account::where('id', 1)->first();
        $this->customer = Customer::factory()->create();
    }

    /** @test */
    public function it_can_show_all_the_projects()
    {
        Project::factory()->create();
        $list = (new ProjectSearch(new ProjectRepository(new Project())))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($list);
    }

    /** @test */
    public function it_can_delete_the_project()
    {
        $project = Project::factory()->create();
        $deleted = $project->deleteEntity();
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_project()
    {
        $project = Project::factory()->create();
        $name = $this->faker->word;
        $data = ['name' => $name];
        $projectRepo = new ProjectRepository($project);
        $updated = $projectRepo->update($data, $project);
        $found = $projectRepo->findProjectById($project->id);
        $this->assertInstanceOf(Project::class, $updated);
        $this->assertEquals($data['name'], $found->name);
    }

    /** @test */
    public function it_can_show_the_project()
    {
        $project = Project::factory()->create();
        $projectRepo = new ProjectRepository(new Project);
        $found = $projectRepo->findProjectById($project->id);
        $this->assertInstanceOf(Project::class, $found);
        $this->assertEquals($project->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_project()
    {
        $data = [
            'account_id'   => $this->account->id,
            'user_id'      => $this->user->id,
            'name'        => $this->faker->word,
            'description'  => $this->faker->sentence,
            'is_completed' => 0,
        ];

        $projectRepo = new ProjectRepository(new Project);
        $factory = (new ProjectFactory())->create($this->user, $this->customer, $this->account);
        $project = $projectRepo->create($data, $factory);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($data['name'], $project->name);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_project_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $product = new ProjectRepository(new Project);
        $product->createProject([]);
    }

    /** @test */
    public function it_errors_finding_a_project()
    {
        $this->expectException(ModelNotFoundException::class);
        $category = new ProjectRepository(new Project);
        $category->findProjectById(999);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
