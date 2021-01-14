<?php

namespace App\Transformations;

use App\Models\File;

class FileTransformable
{
    /**
     * @param File $document
     * @return array
     */
    public function transformFile(File $document)
    {
        return [
            'id'          => $document->id,
            'user_id'     => $document->user_id,
            'assigned_to' => $document->assigned_to,
            'task_id'     => $document->task_id,
            'company_id'  => $document->company_id,
            'file_path'   => (string)url($document->file_path) ?: '',
            'preview'     => (string)$document->preview ?: '',
            'name'        => (string)$document->name,
            'type'        => (string)$document->type,
            'user'        => $document->user,
            'disk'        => (string)$document->disk,
            'hash'        => (string)$document->hash,
            'size'        => (int)$document->size,
            'width'       => (int)$document->width,
            'height'      => (int)$document->height,
            'is_default'  => (bool)$document->is_default,
            'updated_at'  => $document->updated_at,
            'archived_at' => $document->archived_at,
        ];
    }
}
