<?php

namespace App\Http\Controllers;

use App\Jobs\Utils\UploadFile;
use App\Models\CompanyToken;
use App\Models\File;
use App\Repositories\FileRepository;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Requests\Upload\DeleteFile;
use App\Requests\UploadRequest;
use App\Transformations\FileTransformable;
use Exception;
use Illuminate\Http\Response;

class UploadController extends Controller
{
    private $fileRepository;
    private $taskRepository;

    public function __construct(FileRepositoryInterface $fileRepository, TaskRepositoryInterface $taskRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->taskRepository = $taskRepository;
    }

    public function index($entity, $task_id)
    {
        $class = "App\Models\\" . ucfirst($entity);
        $entity = $class::find($task_id);

        $uploads = $this->fileRepository->getFilesForEntity($entity);

        $uploads = $uploads->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();

        return response()->json($uploads);
    }

    /**
     * @param UploadRequest $request
     * @return mixed
     */
    public function store(UploadRequest $request)
    {
        $class = "App\Models\\" . ucfirst($request->entity_type);
        $obj = $class::where('id', $request->entity_id)->first();

        $token_sent = request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;
        $user = $token->user;
        $uploaded_by_customer = !empty($request->input('uploaded_by_customer')) ? true : false;
        $customer_can_view = !empty($request->input('customer_can_view')) && $request->input(
            'customer_can_view'
        ) === 'true' ? true : false;

        $arrAddedFiles = [];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $count => $file) {
                $file = UploadFile::dispatchNow(
                    $file,
                    $user,
                    $account,
                    $obj,
                    $uploaded_by_customer,
                    $customer_can_view
                );

                $arrAddedFiles[$count] = $file;
                $arrAddedFiles[$count]['user'] = $user->toArray();
            }

            return collect($arrAddedFiles)->toJson();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @param DeleteFile $request
     * @return Response
     * @throws Exception
     */
    public function destroy($id, DeleteFile $request)
    {
        $file = $this->fileRepository->findFileById($id);
        $fileRepo = new FileRepository($file);
        $fileRepo->deleteFile();
        return response()->json('File deleted!');
    }
}
