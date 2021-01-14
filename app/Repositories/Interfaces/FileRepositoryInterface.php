<?php

namespace App\Repositories\Interfaces;

use App\Models\File;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{
    public function getFilesForEntity($entity);

    public function createFile(array $data): File;

    public function findFileById(int $id): File;

    public function deleteFile(): bool;

    public function listFiles($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;
}
