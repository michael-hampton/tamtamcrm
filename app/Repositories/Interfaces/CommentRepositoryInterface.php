<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    /**
     *
     * @param Task $objTask
     * @param int $account_id
     */
    public function getAllCommentsForTask(Task $objTask, int $account_id);

    /**
     *
     * @param array $data
     * @return Comment
     * @return Comment
     */
    public function createComment(array $data): Comment;

    /**
     *
     * @param int $id
     * @return Comment
     * @return Comment
     */
    public function findCommentById(int $id): Comment;

    /**
     *
     */
    public function deleteComment(): bool;

    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listComments($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;

    /**
     * @param int $account_id
     * @return Collection
     */
    public function getCommentsForActivityFeed(int $account_id): Collection;

}
