<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait UploadableTrait
{

    /**
     * Upload a single file in the server
     *
     * @param UploadedFile $file
     * @param null $folder
     * @param string $disk
     * @param null $filename
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : str_random(25);
        return $file->storeAs($folder, $name . "." . $file->getClientOriginalExtension(), $disk);
    }

    /**
     * @param UploadedFile $file
     *
     * @param string $folder
     * @param string $disk
     *
     * @return false|string
     */
    public function storeFile(UploadedFile $file, $folder = 'products', $disk = 'public')
    {
        return $file->store($folder, ['disk' => $disk]);
    }

    public function uploadLogo($file)
    {
        $fileName = time() . '.' . $file->extension();
        $path = 'storage/logos';
        $full_path = $path . '/' . $fileName;

        if (!File::isDirectory($path)) {
            File::makeDirectory(public_path($path), 0777, true, true);
        }

        $file->move($path, $fileName);

        return $full_path;
    }
}
