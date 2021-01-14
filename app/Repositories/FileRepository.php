<?php

namespace App\Repositories;

use App\Events\Uploads\FileWasDeleted;
use App\Exceptions\CreateFileErrorException;
use App\Models\File;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\FileRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    /**
     * FileRepository constructor.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        parent::__construct($file);
        $this->model = $file;
    }

    /**
     * @param array $data
     *
     * @return File
     * @throws CreateFileErrorException
     */
    public function createFile(array $data): File
    {
        try {
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateFileErrorException($e);
        }
    }

    /**
     * @param int $id
     *
     * @return File
     * @throws Exception
     */
    public function findFileById(int $id): File
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteFile(): bool
    {
        Storage::delete(public_path($this->model->file_path));
        event(new FileWasDeleted($this->model));
        return $this->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listFiles($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    public function getFilesForEntity($entity)
    {
        return File::where('fileable_id', $entity->id)->where('fileable_type', get_class($entity))
                   ->orderBy('created_at', 'desc')->with('user')->get();
    }
}
