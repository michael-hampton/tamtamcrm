<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ZipDownloadResponse extends StreamedResponse
{
    public function __construct(string $filename)
    {

        $downloadFilename = auth()->user()
            ? auth()->user()->personalDataExportName()
            : basename($filename);


        header("Content-type: application/zip");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: attachment; filename=".$downloadFilename);
        header("Content-length: " . filesize($filename));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$filename");
    }
}