<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\Task;
use App\Models\User;
use App\Repositories\FileRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private $user;
    private $task;

    /**
     * @var int
     */
    private $account_id = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create();
    }

    /** @test */
    public function it_can_show_all_the_files()
    {
        $insertedfile = File::factory()->create();
        $fileRepo = new FileRepository(new File);
        $list = $fileRepo->listFiles()->toArray();
        $myLastElement = end($list);
        // $this->assertInstanceOf(Collection::class, $list);
        $this->assertEquals($insertedfile->toArray(), $myLastElement);
    }

    /** @test */
    public function it_can_delete_the_file()
    {
        $file = File::factory()->create();
        $fileRepo = new FileRepository($file);
        $deleted = $fileRepo->deleteFile($file->id);
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_show_the_file()
    {
        $file = File::factory()->create();
        $fileRepo = new FileRepository(new File);
        $found = $fileRepo->findFileById($file->id);
        $this->assertInstanceOf(File::class, $found);
        $this->assertEquals($file->name, $found->name);
    }

    /** @test */
    public function it_can_create_a_file()
    {
        $data = [
            'account_id'    => $this->account_id,
            'fileable_id'   => $this->task->id,
            'fileable_type' => 'App\Models\Task',
            'user_id'       => $this->user->id,
            'name'          => $this->faker->word,
        ];

        $fileRepo = new FileRepository(new File);
        $file = $fileRepo->createFile($data);

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals($data['name'], $file->name);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_file_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $product = new FileRepository(new File);
        $product->createFile([]);
    }

    /** @test */
    public function it_errors_finding_a_file()
    {
        $this->expectException(ModelNotFoundException::class);
        $file = new FileRepository(new File);
        $file->findFileById(999);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
