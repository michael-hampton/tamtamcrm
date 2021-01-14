<?php

namespace App\Http\Controllers;

use App\Factory\CommentFactory;
use App\Repositories\CommentRepository;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Requests\CommentRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    /**
     * @var CommentRepositoryInterface
     */
    private $comment_repo;

    /**
     * @var TaskRepositoryInterface
     */
    private $task_repo;

    /**
     * CommentController constructor.
     *
     * @param CommentRepositoryInterface $comment_repo
     * @param TaskRepositoryInterface $task_repo
     */
    public function __construct(CommentRepositoryInterface $comment_repo, TaskRepositoryInterface $task_repo)
    {
        $this->comment_repo = $comment_repo;
        $this->task_repo = $task_repo;
    }

    public function index($entity, $entity_id)
    {
        $class = 'App\Models\\' . $entity;
        $entity = $class::where('id', $entity_id)->first();
        $comments = $entity->comments()->with('user')->get();
        return response()->json($comments);
    }

    /**
     *
     * @param CommentRequest $request
     * @return type
     */
    public function store(CommentRequest $request)
    {
        $class = strpos($request->input('entity'), 'Models') !== false ? $request->input(
            'entity'
        ) : 'App\Models\\' . $request->input('entity');

        $entity = $class::where('id', $request->input('entity_id'))->first();

        $validatedData = $request->validated();

        $user = Auth::user();

        $data = [
            'parent_id'   => !empty($validatedData['parent_id']) ? $validatedData['parent_id'] : 0,
            'comment'     => $validatedData['comment'],
            'parent_type' => (int)!empty($validatedData['task_id']) ? 1 : 2,
            'user_id'     => $user->id
        ];

        $comment = CommentFactory::create(auth()->user()->id, auth()->user()->account_user()->account_id);
        $comment->fill($data);
        $entity->comments()->save($comment);

        if (!empty($validatedData['task_id'])) {
            $task = $this->task_repo->findTaskById($validatedData['task_id']);
            $task->comments()->attach($comment);
        }

        $arrResponse[0] = $comment;
        $arrResponse[0]['user'] = $user->toArray();

        //send notification

        //Notification::send($user, new CommentCreated($comment));

        return collect($arrResponse)->toJson();
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id)
    {
        $comment = $this->comment_repo->findCommentById($id);
        $commentRepo = new CommentRepository($comment);
        $commentRepo->deleteComment();

        return response()->json('Comment deleted!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, $id)
    {
        $comment = $this->comment_repo->findCommentById($id);
        $update = new CommentRepository($comment);
        $update->updateComment($request->all());
        return response()->json('Comment updated!');
    }

}
