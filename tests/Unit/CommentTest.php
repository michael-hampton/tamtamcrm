<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Repositories\CommentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private $user;
    private $task;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create();
    }

    /** @test */
    public function it_can_show_all_the_comments()
    {
        $insertedcomment = Comment::factory()->create();
        $commentRepo = new CommentRepository(new Comment);
        $list = $commentRepo->listComments()->toArray();
        $myLastElement = end($list);
        // $this->assertInstanceOf(Collection::class, $list);
        $this->assertEquals($insertedcomment->toArray(), $myLastElement);
    }

    /** @test */
    public function it_can_delete_the_comment()
    {
        $comment = Comment::factory()->create();
        $commentRepo = new CommentRepository($comment);
        $deleted = $commentRepo->deleteComment($comment->id);
        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_update_the_comment()
    {
        $comment = Comment::factory()->create();
        $data = [
            'comment' => $this->faker->sentence
        ];

        $commentRepo = new CommentRepository($comment);
        $updated = $commentRepo->updateComment($data);
        $found = $commentRepo->findCommentById($comment->id);
        $this->assertTrue($updated);
        $this->assertEquals($data['comment'], $found->comment);
    }

    /** @test */
    public function it_can_show_the_comment()
    {
        $comment = Comment::factory()->create();
        $commentRepo = new CommentRepository(new Comment);
        $found = $commentRepo->findCommentById($comment->id);
        $this->assertInstanceOf(Comment::class, $found);
        $this->assertEquals($comment->comment, $found->comment);
    }

    /** @test */
    public function it_can_attach_a_task()
    {
        $task = Task::factory()->create();
        $comment = Comment::factory()->create();
        $response = $task->comments()->create($comment->toArray());
        $this->assertDatabaseHas(
            'comments',
            [
                'comment' => $comment->comment
            ]
        );
    }

    /** @test */
    public function it_can_create_a_comment()
    {
        $data = [
            'account_id' => 1,
            'user_id'    => $this->user->id,
            'comment'    => $this->faker->sentence,
        ];

        $commentRepo = new CommentRepository(new Comment);
        $comment = $commentRepo->createComment($data);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($data['comment'], $comment->comment);
    }

    /**
     * @codeCoverageIgnore
     */
    public function it_errors_creating_the_comment_when_required_fields_are_not_passed()
    {
        $this->expectException(QueryException::class);
        $comment = new CommentRepository(new Comment);
        $comment->createComment([]);
    }

    /** @test */
    public function it_errors_finding_a_comment()
    {
        $this->expectException(ModelNotFoundException::class);
        $comment = new CommentRepository(new Comment);
        $comment->findCommentById(999);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
