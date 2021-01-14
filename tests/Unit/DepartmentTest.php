<?php

namespace Tests\Unit;

use App\Factory\DepartmentFactory;
use App\Models\Department;
use App\Models\User;
use App\Repositories\DepartmentRepository;
use App\Transformations\DepartmentTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DepartmentTest extends TestCase
{

    use DatabaseTransactions, DepartmentTransformable, WithFaker;

    private $account_id = 1;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_transform_the_department()
    {
        $department = Department::factory()->create();
        $cust = $this->transformDepartment($department);
        $this->assertIsString($cust->name);
    }

    /** @test */
    public function it_can_delete_a_department()
    {
        $department = Department::factory()->create();
        $departmentRepo = new DepartmentRepository($department);
        $delete = $departmentRepo->deleteDepartment();
        $this->assertTrue($delete);
        //$this->assertDatabaseHas('departments', $department->toArray());
    }

    /** @test */
    public function it_fails_when_the_department_is_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $department = new DepartmentRepository(new Department);
        $department->findDepartmentById(999);
    }

    /** @test */
    public function it_can_find_a_department()
    {
        $user = User::factory()->create();
        $data = [
            'name'               => $this->faker->name,
            'department_manager' => $user->id,
        ];

        $department = new DepartmentRepository(new Department);
        $factory = (new DepartmentFactory())->create($this->account_id, $this->user->id);
        $created = $department->save($data, $factory);
        $found = $department->findDepartmentById($created->id);
        $this->assertInstanceOf(Department::class, $found);
        $this->assertEquals($data['name'], $found->name);
        $this->assertEquals($data['department_manager'], $found->department_manager);
    }

    /** @test */
    public function it_can_update_the_department()
    {
        $department = Department::factory()->create();
        $user = User::factory()->create();
        //$parent = factory(CategorySearch::class)->create();
        $params = [
            'name'               => $this->faker->name,
            'department_manager' => $user->id,
            'parent'             => 0,
        ];

        $departmentRepo = new DepartmentRepository($department);
        $updated = $departmentRepo->save($params, $department);
        $this->assertInstanceOf(Department::class, $updated);
        $this->assertEquals($params['name'], $updated->name);
        $this->assertEquals($params['department_manager'], $updated->department_manager);
        $this->assertEquals($params['parent'], $updated->parent_id);
    }

    /** @test */
    public function it_can_create_a_department()
    {
        $factory = (new DepartmentFactory)->create($this->account_id, $this->user->id);
        $user = User::factory()->create();

        $data = [
            'name'               => $this->faker->name,
            'department_manager' => $user->id
        ];
        $department = new DepartmentRepository(new Department);
        $created = $department->save($data, $factory);
        $this->assertInstanceOf(Department::class, $created);
        $this->assertEquals($data['name'], $created->name);
        $this->assertEquals($data['department_manager'], $created->department_manager);
        $collection = collect($data);
        $this->assertDatabaseHas('departments', $collection->all());
    }

    /*public function it_errors_creating_the_department_when_required_fields_are_not_passed()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $task = new DepartmentRepository(new Department);
        $task->createDepartment([]);
    }*/

    /** @test */
    public function it_can_list_all_departments()
    {
        Department::factory()->create();
        $departmentRepo = new DepartmentRepository(new Department);
        $list = $departmentRepo->listDepartments();
        $this->assertInstanceOf(Collection::class, $list);
    }

    /** @test */
    public function it_can_create_root_department()
    {
        $factory = (new DepartmentFactory())->create($this->account_id, $this->user->id);
        $user = User::factory()->create();

        $params = [
            'name'               => $this->faker->name,
            'department_manager' => $user->id,
        ];

        $department = new DepartmentRepository(new Department);
        $created = $department->save($params, $factory);

        $this->assertTrue($created->isRoot());
    }

    /** @test */
    public function it_can_update_child_department_to_root_category()
    {
        // suppose to have a child category
        $parent = Department::factory()->create();
        $child = Department::factory()->create();
        $child->parent()->associate($parent)->save();
        // send params without parent
        $department = new DepartmentRepository($child);
        $updated = $department->save(
            [
                'name' => 'Boys',
            ],
            $child
        );
        // check if updated category is root
        $this->assertTrue($updated->isRoot());
    }

    /** @test */
    public function it_can_update_root_category_to_child()
    {
        $child = Department::factory()->create();
        $parent = Department::factory()->create();
        $name = $this->faker->name;

        // set parent category via repository
        $department = new DepartmentRepository($child);
        $updated = $department->save(
            [
                'name'   => $name,
                'parent' => $parent->id
            ],
            $child
        );

        // check if updated category is root
        $this->assertEquals($updated->name, $name);
        $this->assertEquals($updated->parent_id, $parent->id);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
