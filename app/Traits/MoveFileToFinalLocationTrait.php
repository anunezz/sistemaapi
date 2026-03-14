<?php

namespace App\Traits;

use App\Models\Archivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;

trait MoveFileToFinalLocationTrait {

    use CheckSpaceNfsTrait;

    /**
     * @throws \Exception
     */
    public function moveFileLocation($path,$locationOfOrigin)
    {
        try {
            $newLocationPath = str_replace(['/media-file'], '', $path);
            $locationOfOrigin = str_replace(['/media-file'], '', $locationOfOrigin);


            if (Storage::disk('local')->exists($locationOfOrigin)) {

                Storage::disk('local')->move($locationOfOrigin, $newLocationPath);

                $extension = strtolower(\File::extension($newLocationPath));

                $this->optimizeImage($locationOfOrigin, $newLocationPath,$extension);

                if (Storage::getDefaultDriver() === "nfs" && $this->getStorageInformation()) {
                    $fileContent = Storage::disk('local')->get($newLocationPath);
                    Storage::disk('nfs')->put($newLocationPath, $fileContent);

                    if (Storage::disk('local')->exists($newLocationPath) && Storage::disk('nfs')->exists($newLocationPath)) {
                        Storage::disk('local')->delete($newLocationPath);
                    }
                }
            }
        }
        catch ( \Exception $e ){
             \Log::error($e->getMessage());
        }
    }

    public function deleteUserDirectory($deletePathTemp)
    {
        if ($deletePathTemp !== ''){
            Storage::disk('local')->deleteDirectory($deletePathTemp);
        }
    }

    public function optimizeImage($oldPath, $newPath, $extension)
    {
        // dd($oldPath, $newPath, $extension);
        $auxPath = dirname($oldPath);
        $newName = pathinfo($oldPath, PATHINFO_FILENAME) . '_optimized.' . $extension;
        $oldFullPath = $auxPath . '/optimized/' . $newName;

        $auxPath2 = dirname($newPath);
        $newName2 = pathinfo($newPath, PATHINFO_FILENAME) . '_optimized.' . $extension;
        $newFullPath = $auxPath2 . '/optimized/' . $newName2;


        // dd(
        //     $oldFullPath, $newFullPath
        // );

        if (Storage::disk('local')->exists($oldFullPath)) {
            Storage::disk('local')->move($oldFullPath, $newFullPath);

            // Intentar mover optimizado a NFS
            if (Storage::getDefaultDriver() === "nfs" && $this->getStorageInformation()) {
                try {
                    $fileContent = Storage::disk('local')->get($newFullPath);
                    Storage::disk('nfs')->put($newFullPath, $fileContent);

                    if (
                        Storage::disk('local')->exists($newFullPath) &&
                        Storage::disk('nfs')->exists($newFullPath)
                    ) {
                        Storage::disk('local')->delete($newFullPath);
                    }
                } catch (\Throwable $e) {
                    \Log::warning("Error al mover archivo optimizado a NFS: " . $e->getMessage(), [
                        'path' => $newFullPath,
                    ]);
                }
            }
        }
    }

}
