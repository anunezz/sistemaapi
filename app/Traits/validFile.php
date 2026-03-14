<?php

namespace App\Traits;


use SoftCreatR\MimeDetector\MimeDetector;
use SoftCreatR\MimeDetector\MimeDetectorException;
ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');

trait validFile{
    public function validateFile($file, $system_file_types = null,$path='')
    {
        $allowedFiles = [];
        $maximumFileSize = ( $system_file_types === 12 ? 3 : 100 ) * 1_048_576; // 5 MB in bytes
        $response = ["typeFile" => false, "maxSize" => false];

        $allowedFiles = ['pdf'];

        if (
            $system_file_types === 12
        ){
            //$allowedFiles = ["png", "jpg", "jpeg", "gif", "PNG", "JPG", "JPEG", "GIF"];
            $allowedFiles = ["png", "jpg", "jpeg", "PNG", "JPG", "JPEG"];
        }

        $mimeDetector = new MimeDetector($path);
        $fileExtension = $mimeDetector->getFileExtension();

        $info = new \SplFileInfo($file->getClientOriginalName());
        $fileSize = $file->getSize();

        if ($fileExtension !== '' && $fileExtension !== null) {
            foreach ($allowedFiles as $ext) {
                if ($ext === $fileExtension && $info->getExtension() === $ext) {
                    $response["typeFile"] = true;
                }
            }
            if ($fileSize <= $maximumFileSize) {
                $response["maxSize"] = true;
            }
        }

        if ($response["typeFile"] && $response["maxSize"]) {
            return true;
        }

        return false;
    }
}
