<?php

namespace App\Http\Controllers;


use App\Traits\EscapeTextTrait;
use App\Traits\validFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FilesChunksController extends Controller
{
    use validFile,EscapeTextTrait;

    private $limitViewFilesImages = 5 * 1024 * 1024; // Limite de tamaño de archivo para vista previa sin optimiza (5 MB) de lo contrario se optimiza la imagen
    private $allowedExtensionsImages = ['jpg', 'jpeg', 'png', 'gif']; // Extensiones de imagen permitidas para optimización]
    public function __invoke(Request $request)
    {
        try {
            $file = $request->file('file');
            $offset = (int) $request->input('offset');
            $fileSize = (int) $request->input('file_size');

            $fileName = $this->escapeText($request->get('file_name'));
            $fileNameOriginal = $fileName;
            $fileName = "{$fileSize}_{$fileName}";

            $userId = auth()->id();
            $chunksDir = "{$request->input('save_storage_cunks_folder')}/$userId";
            $chunkPath = "$chunksDir/$fileName";

            // Si se completó la carga anteriormente
            if ($request->boolean('full_load')) {
                return $this->handleFullLoad($request, $chunkPath, $fileNameOriginal);
            }

            // Eliminar archivo vacío si es el primer chunk
            $this->deleteEmptyFileIfFirstChunk($chunkPath, $offset);

            if ($this->isFileAlreadyUploaded($chunkPath, $offset)) {
                $size = Storage::disk('local')->size($chunkPath);

                if ($this->isFileFullyUploaded($fileSize, $size)) {
                    return $this->handleFullLoad($request, $chunkPath, $fileNameOriginal);
                }

                return response()->json([
                    'success' => true,
                    'file_exist' => true,
                    'size' => $size
                ]);
            }

            // Asegurarse de que el directorio exista
            if (!Storage::disk('local')->exists($chunksDir)) {
                Storage::disk('local')->makeDirectory($chunksDir);
            }

            // Escribir chunk en la posición correspondiente
            $this->writeChunkToFile($chunkPath, $offset, $file->get());

            // Si es el último fragmento, mover archivo a su destino final
            if ($request->boolean('is_last')) {
                return $this->handleFullLoad($request, $chunkPath, $fileNameOriginal);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function deleteEmptyFileIfFirstChunk(string $path, int $offset): void
    {
        if (
            Storage::disk('local')->exists($path) &&
            Storage::disk('local')->size($path) === 0 &&
            $offset === 0
        ) {
            Storage::disk('local')->delete($path);
        }
    }

    private function isFileAlreadyUploaded(string $path, int $offset): bool
    {
        return Storage::disk('local')->exists($path) && $offset === 0;
    }

    private function isFileFullyUploaded(int $expectedSize, int $actualSize): bool
    {
        return $this->formatSizeUnits($expectedSize) === $this->formatSizeUnits($actualSize);
    }

    private function writeChunkToFile(string $path, int $offset, string $content): void
    {
        $fullPath = Storage::disk('local')->path($path);
        $fp = fopen($fullPath, 'a+');
        fseek($fp, $offset);
        fwrite($fp, $content);
        fclose($fp);
    }

    private function handleFullLoad(Request $request, string $chunkPath, string $fileName)
    {
        $response = $this->moveFile($request, $chunkPath, $fileName);
        return response()->json($response, 200);
    }

    public function moveFile(Request $request, string $path, string $originalFileName): array
    {
        $userId = auth()->id();
        $mimeType = $request->input('mimeType');
        $systemFileType = $request->integer('type_file_back_system', null);

        if ($mimeType) {
            Storage::disk('local')->append($path, "Content-Type: {$mimeType}\n\n");
        }


        $localPath = Storage::disk('local')->path($path);
        $file = new \Illuminate\Http\UploadedFile($localPath, $originalFileName);
        //convertir la extension a minusculas para evitar problemas
        $extension = strtolower($file->getExtension());

        //obtener el tamaño del archivo
        $fileSize = Storage::disk('local')->size($path);


        if (!$this->validateFile($file, $systemFileType,$localPath)) {
            Storage::disk('local')->delete($path);
            return ['success' => false, 'message' => "Tipo de archivo no válido"];
        }

        // Generar nombre nuevo con hash
        $today = date("Y-m-d");
        $hashedName = "{$today}_{$userId}_" . Str::random(40) . '.' . $extension;
        $finalFolder = "{$request->input('save_storage_folder')}/$userId";
        $finalPath = "$finalFolder/$hashedName";

        Storage::disk('local')->makeDirectory($finalFolder);
        Storage::disk('local')->move($path, $finalPath);

        $this->optimizeImage($finalPath,$extension,$fileSize);

        return [
            'file_fully_uploaded' => true,
            'success' => true,
            'fileName' => $originalFileName,
            'fileNameHash' => $hashedName,
            'file_location' => $finalPath,
            'path_location_temp' => $finalFolder,
            'typeFile' => $extension,
            'path' => "/media-file/$finalPath"
        ];
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return "$bytes bytes";
        } elseif ($bytes === 1) {
            return "1 byte";
        }
        return '0 bytes';
    }

    public function optimizeImage ($oldPath,$extension,$fileSize)
    {
        $auxPath = dirname($oldPath);
        $newName = pathinfo($oldPath, PATHINFO_FILENAME) . '_optimized.' . $extension;
        $directoryOptimized = $auxPath . '/optimized/';
        $fullPath = $directoryOptimized . $newName;

        //check if the directory exists, if not create it
        if (!Storage::disk('local')->exists($directoryOptimized)) {
            Storage::disk('local')->makeDirectory($directoryOptimized);
        }


        if (in_array($extension, $this->allowedExtensionsImages, true) && $fileSize > $this->limitViewFilesImages) {


            $manager = new ImageManager(new Driver());
            $image = $manager->read(Storage::disk('local')->path($oldPath));


            // Redimensionar la imagen si es necesario
            // Redimensionar si el ancho es muy grande (mantiene el aspecto)
            if ($image->width() > 900) {
                $image->scaleDown(width: 900);
            }

            // Guardar la imagen optimizada
            $image->save(Storage::disk('local')->path($fullPath), quality: 10, progressive: true);

        }
    }
}
