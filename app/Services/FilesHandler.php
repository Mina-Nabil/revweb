<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FilesHandler
{

    public function uploadFile($requestFile, $path)
    {
        return $requestFile->store($path, "s3");
    }

    public function deleteFile($path)
    {
        if ($path)
            return Storage::disk('s3')->delete($path); //or false
    }
}
