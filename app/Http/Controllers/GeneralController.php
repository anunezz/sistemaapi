<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('max_execution_time', 0);

class GeneralController extends Controller
{
    private $type_images = ['jpeg','jpeg', 'jpg', 'png','gif'];
    private $image_quality = 10; //0 a 100

    private $limitViewFilesImages = 5 * 1024 * 1024; // Limite de tamaño de archivo para vista previa (5 MB)
    private $limitViewFilesPdf = 13 * 1024 * 1024; // Limite de tamaño de archivo para vista previa (13 MB)

    public function downloadAnyFile(Request $request)
    {
        try {
            if (!$request->wantsJson()) {
                return response()->view('errors.500', [], 500);
            }

            $data = $request->all();

            if (!isset($data['path'])) {
                return response()->view('errors.500', [], 500);
            }

            $path = str_replace(['/media-file'], '', $data['path']);
            $defaultDisk = config('filesystems.default');

            // Prioriza disco local si existe el archivo
            $disk = Storage::disk('local')->exists($path) ? 'local' : (Storage::exists($path) ? $defaultDisk : null);

            if (!$disk || !Storage::disk($disk)->exists($path)) {
                return response()->view('errors.500', [], 500);
            }

            $file = Storage::disk($disk)->get($path);
            $mimeType = Storage::disk($disk)->mimeType($path);

            $response = response()->make($file, 200);
            $response->header('Content-Type', $mimeType);
            $response->header('charset', 'utf-8');

            return $response;
        } catch (\Exception $e) {
            \Log::error(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], JSON_THROW_ON_ERROR));

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar descargar el archivo.'
            ], 500);
        }
    }

    public function getMediaFiles ($path)
    {
        try {
            //obtener la extensión del archivo con Storage
            $extension = strtolower(\File::extension($path));
            $isImage = in_array($extension, $this->type_images);
            $defaultDisk = config('filesystems.default'); // s3, nfs, etc.


            // Prioriza disco local si existe el archivo
            $disk = Storage::disk('local')->exists($path) ? 'local' : (Storage::exists($path) ? $defaultDisk : null);


            if (!$disk) {
                return response()->view('errors.500', [], 500);
            }

            // Obtiene el contenido del archivo en caso de que sea una imagen
            if ($isImage) {

                $auxPath = dirname($path);
                $newName = pathinfo($path, PATHINFO_FILENAME) . '_optimized.' . $extension;
                $fullPath = $auxPath . '/optimized/' . $newName;

                if (Storage::disk($disk)->exists($fullPath)){
                    $response = response()->make(Storage::disk($disk)->get($fullPath), 200);
                    $response->header('Content-Type', Storage::disk($disk)->mimeType($fullPath));
                    $response->header('charset', 'utf-8');
                    return $response;
                }


                $size = Storage::disk($disk)->size($path);
                $manager = new ImageManager(new Driver());
                $image = $manager->read(Storage::disk($disk)->path($path));




                if ($size > $this->limitViewFilesImages) {

                    \Log::info('inicio redimensionamiento de imagen: ' . $path);
                    // Redimensionar si el ancho es muy grande (mantiene el aspecto)
                    if ($image->width() > 900) {
                        $image->scaleDown(width: 900);
                    }

                    // Guardar la imagen optimizada
                   $image->save(Storage::disk($disk)->path($fullPath), quality: $this->image_quality, progressive: true);
                    return response()->file(Storage::disk($disk)->path($fullPath));
                } else {

                    $encodedImage = $image->encodeByMediaType(quality: $this->image_quality);
                }
                $response = response($encodedImage, 200);
            } else {

                //verifica si es un archivo grande de más de 13MB
                $isLarge = Storage::disk($disk)->size($path) > $this->limitViewFilesPdf;

                if ($isLarge) {
                    // Regresar el PDF generico para archivos grandes
//                    $content = Storage::disk('local')->get("watermark/PDF_LARGE_FILE.pdf");
                    $stream = Storage::disk($disk)->readStream($path);

                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                        fclose($stream);
                    }, 200, [
                        'Content-Type' => Storage::disk($disk)->mimeType($path) ?? 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
                    ]);
                } else {
                    $content = Storage::disk($disk)->get($path);
                }
                $response = response()->make($content, 200);
            }


            $response->header('Content-Type', Storage::disk($disk)->mimeType($path));
            $response->header('charset', 'utf-8');

            return $response;
        }
        catch ( \Exception $e ){
            \Log::info(json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getLine(),
                'file' => $e->getFile()
            ], JSON_THROW_ON_ERROR));
        }
    }
    public function getImageFilesWatermarkPDF($path)
{
    try {
        $image_quality_pdf = 20;
        $defaultDisk = config('filesystems.default'); // s3, nfs, etc.

        // Prioriza disco local si existe el archivo
        $disk = Storage::disk('local')->exists($path)
            ? 'local'
            : (Storage::exists($path) ? $defaultDisk : null);

        if (!$disk || !Storage::disk($disk)->exists($path)) {
            return null;
        }

        // Crear el ImageManager con el driver GD
        $imageManager = new ImageManager(new Driver());

        // Leer la imagen principal
        $image = $imageManager->read(Storage::disk($disk)->get($path));
        $image->scaleDown(width: 1500); // reemplazo de widen()


        // Codificar la imagen final a JPEG y obtener el string
        $encoded = $image->toJpeg(quality: $image_quality_pdf);

        // Retornar en base64
        return base64_encode((string) $encoded);

    } catch (\Exception $e) {
        \Log::info(json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], JSON_THROW_ON_ERROR));

        return null;
    }
}
}
