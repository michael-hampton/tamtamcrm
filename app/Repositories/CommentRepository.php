<?php

namespace App\Repositories;

use App\Exceptions\CreateCommentErrorException;
use App\Models\Comment;
use App\Models\Task;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{

    /**
     * CommentRepository constructor.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        parent::__construct($comment);
        $this->model = $comment;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function updateComment(array $data): bool
    {
        return $this->update($data);
    }

    /**
     * @param array $data
     *
     * @return Comment
     * @throws CreateCommentErrorException
     */
    public function createComment(array $data): Comment
    {
        try {
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateCommentErrorException($e);
        }
    }

    /**
     * @param int $id
     *
     * @return Comment
     * @throws Exception
     */
    public function findCommentById(int $id): Comment
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteComment(): bool
    {
        return $this->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listComments($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    public function getAllCommentsForTask(Task $objTask, int $account_id): Collection
    {
        return $this->model->join('comment_task', 'comments.id', '=', 'comment_task.comment_id')
                           ->where('comment_task.task_id', $objTask->id)->where('comments.account_id', $account_id)
                           ->where('comments.parent_type', '=', 1)->orderBy('created_at', 'desc')->with('user')->get();
    }

    /**
     *
     * @param int $account_id
     * @return Collection
     */
    public function getCommentsForActivityFeed(int $account_id): Collection
    {
        return $this->model->where('parent_type', 2)->where('account_id', '=', $account_id)
                           ->orderBy('created_at', 'desc')->with('user')->get();
    }

    public function save(array $data, Comment $comment)
    {
        $comment->fill($data);
        $comment->save();
        return $comment;
    }

}
