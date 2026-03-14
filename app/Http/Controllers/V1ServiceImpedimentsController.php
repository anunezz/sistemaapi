<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\ImImpedimento;
use App\Models\ImSolicitud;
use App\Models\ImSolicitudDocumento;
use App\Models\Catalogs\ImCatOficina;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ConsultaVerificacionRequest;
use App\Http\Requests\ConsultaVerificacionRechazadosRequest;
use App\Http\Requests\SolicitudAltaModificacionRequest;
use App\Http\Requests\SolicitudAltaRequest;
use App\Http\Requests\AlertaImpedimentosRequest;
use App\Http\Requests\SolicitudVerificacionRequest;
use App\Models\ImCatAnexos;
use Illuminate\Support\Facades\Log;

class V1ServiceImpedimentsController extends Controller
{
    //TODO solicitud_alta_modificacion el campo probatorio_otro queda de sobra y no existe en este catalogo otro especificar
    public function solicitud_alta_modificacion(SolicitudAltaModificacionRequest $request){
        DB::beginTransaction();
        try{
            $request = self::transformar_en_mayusculas( $request->all() );
            $ImSolicitud = ImSolicitud::create([
                "id_tipo_solicitud" => 4,
                "id_estatus_solicitud" => 10,
                "id_prioridad" => 3,
                "id_oficina" => $request->id_oficina,
                "correo_electronico" => $request->correo_electronico,
                "curp" => $request->curp,
                "nombres" => $request->nombres,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => ( $request->fecha_nacimiento ? Carbon::parse($request->fecha_nacimiento)->format("Y-m-d") : null ),
                "fecha_registro" => Carbon::now(),
                "curp_identidad" => $request->curp_identidad,
                "nombres_identidad" => $request->nombres_identidad,
                "primer_apellido_identidad" => $request->primer_apellido_identidad,
                "segundo_apellido_identidad" => $request->segundo_apellido_identidad,
            ]);

            if( isset( $request->foto ) && $request->foto ){
                $foto =  $this->save_file_base64($request->foto, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 12,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            if( isset($request->probatorio_nacionalidad) && $request->probatorio_nacionalidad ){
                $foto =  $this->save_file_base64($request->probatorio_nacionalidad, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 10,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            if( isset($request->probatorio_identidad) && $request->probatorio_identidad ){
                $foto =  $this->save_file_base64($request->probatorio_identidad, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 9,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "La solicitud de alta de modificacion fue creada exitosamente con el id solicitud ".$ImSolicitud->id_solicitud,
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    //TODO solicitud de verificacion ESTOS CAMPOS EN EL POSTMAN ESTA EN DUDA "no_solicitud", "bol_permiso"
    //TODO no existe probatorio identidad
    public function solicitud_verificacion(SolicitudVerificacionRequest $request){
        DB::beginTransaction();
        try{
            $request = self::transformar_en_mayusculas( $request->all() );
            $ImSolicitud = ImSolicitud::create([
                "id_tipo_solicitud" => 3,
                "id_estatus_solicitud" => 10,
                "id_estatus_verificacion" => 1,
                "id_prioridad" => 3,
                "id_oficina" => $request->id_oficina,
                "correo_electronico" => $request->correo_electronico,
                "curp" => $request->curp,
                "nombres" => $request->nombres,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => ( $request->fecha_nacimiento ? Carbon::parse($request->fecha_nacimiento)->format("Y-m-d") : null ),
                "entidad_federativa_nacimiento" => $request->entidad_federativa_nacimiento,
                "fecha_registro" => Carbon::now()
            ]);

            if( isset($request->foto) && $request->foto ){
                $foto =  $this->save_file_base64($request->foto, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 12,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            if( isset($request->probatorio_nacionalidad) && $request->probatorio_nacionalidad ){
                $foto =  $this->save_file_base64($request->probatorio_nacionalidad, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 10,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            if( isset($request->probatorio_identidad) && $request->probatorio_identidad ){
                $foto =  $this->save_file_base64($request->probatorio_identidad, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 9,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "La solicitud de verificación fue creada exitosamente con el id solicitud ".$ImSolicitud->id_solicitud,
                'id_solicitud' => $ImSolicitud->id_solicitud
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    //TODO solicitud_alta Probatorio_otro  =  probatorio nacionalidad no se encuentra en el catalogo de las altas pero se ingresa como otro especificar [pr lo tanto el campo probatorio_otro tambien va en otro especificar
    public function solicitud_alta(SolicitudAltaRequest $request){
        DB::beginTransaction();
        try{
            $request = self::transformar_en_mayusculas( $request->all() );
            $ImSolicitud = ImSolicitud::create([
                "id_tipo_solicitud" => 1,
                "id_estatus_solicitud" => 10,
                "id_prioridad" => 3,
                "id_oficina" => $request->id_oficina,
                "correo_electronico" => $request->correo_electronico,
                "curp" => $request->curp,
                "nombres" => $request->nombres,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => ( $request->fecha_nacimiento ? Carbon::parse($request->fecha_nacimiento)->format("Y-m-d") : null ),
                "entidad_federativa_nacimiento" => $request->entidad_federativa_nacimiento,
                "fecha_registro" => Carbon::now()
            ]);

            if( isset( $request->foto ) &&  $request->foto ){
                $foto =  $this->save_file_base64($request->foto, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 12,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            if( isset($request->probatorio_nacionalidad) && $request->probatorio_nacionalidad ){
                $foto =  $this->save_file_base64($request->probatorio_nacionalidad, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 4,
                        'observaciones' => ImCatAnexos::find(10)->nombre,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            if( isset($request->probatorio_identidad) && $request->probatorio_identidad ){
                $foto =  $this->save_file_base64($request->probatorio_identidad, $ImSolicitud->id_solicitud);
                if( $foto->success == true ){
                    ImSolicitudDocumento::create([
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'id_cat_anexos' => 9,
                        'identificador_documento' => $foto->name,
                        'fecha_documento' => Carbon::now(),
                        'url_documento' => $foto->relativePath,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "La solicitud de alta fue creada exitosamente con el id solicitud ".$ImSolicitud->id_solicitud,
            ], 200);


        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    public function consulta_de_impedimentos(Request $request){
        DB::beginTransaction();
        try{

            $ImImpedimento = ImImpedimento::whereHas('people', function ($q) use ( $request ) {
                return $q->where('curp',$request->curp)
                ->where('nombres',$request->nombres)
                ->where('primer_apellido',$request->primer_apellido)
                ->where('segundo_apellido',$request->segundo_apellido)
                ->where('fecha_nacimiento',$request->fecha_nacimiento)
                ->where('bol_eliminado',false);
            })->where('bol_eliminado',false)
            ->where('id_estatus_impedimento',100)
            ->where('id_oficina',$request->id_oficina)
            ->get();

            $response = [
                'success' => true
            ];

            if( $ImImpedimento->count() > 0 ){
                $response["Impedimentos"] = $ImImpedimento->pluck("numero_impedimento");
                $response["message"] = "Se encontraron ".$ImImpedimento->count()." impedimentos activos.";
            }else{
                $response["message"] = "No encontraron impedimentos activos.";
            }

            return response()->json($response, 200);


        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    public function alerta_de_impedimentos(AlertaImpedimentosRequest $request){
        DB::beginTransaction();
        try{
                $aux = false;
                $ImImpedimento1 = ImImpedimento::whereHas('people', function ($q) use ( $request ) {
                //$ImImpedimento1 = ImImpedimento::with(['people'])->whereHas('people', function ($q) use ( $request ) {
                    return $q->SearchService( $request );
                })
                ->where('id_estatus_impedimento',100)
                ->where('bol_eliminado', false)
                ->whereDoesntHave('low')
                ->exists();

                $ImImpedimento2 = ImImpedimento::whereHas('people', function ($q) use ( $request ) {
                    return $q->SearchService( $request );
                })
                ->where('id_estatus_impedimento',100)
                ->where('bol_eliminado', false)
                ->whereHas('low', function ($q){
                    return $q->where('id_estatus_impedimento_baja',100)
                            ->where('bol_eliminado',true);
                })
                ->exists();

                if( $ImImpedimento1 || $ImImpedimento2 ){
                    $aux =  true;
                }

                return response()->json([
                    'success' => true,
                    'Impedimentos' => $aux
                ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    public function consulta_verficacion(ConsultaVerificacionRequest $request){
        DB::beginTransaction();
        try{
            if($request->wantsJson()){

                $ImSolicitud = ImSolicitud::with([
                    "cat_status_verificacion",
                    "cat_status"
                ])->find( $request->id_solicitud );

                $results = [];
                $message = "";
                if( $ImSolicitud ){
                    $message = "Se encontro la solicitud de verificación con el id_solicitud ".$ImSolicitud->id_solicitud.".";
                    $results = [
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'estatus' => optional($ImSolicitud->cat_status_verificacion)->estatus ?? null,
                        'bol_estatus' => optional($ImSolicitud->cat_status_verificacion)->bol_estatus ?? null,
                        'id_estatus_verificacion' => optional($ImSolicitud->cat_status_verificacion)->id_estatus_verificacion ?? null,
                    ];
                }else{
                    $message = "No encontraron solicitudes de verificación.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'Solicitud' => $results,
                ], 200);
            }else{
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (Exception $e) {
            dd(444,$e);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    public function consulta_verficacion_rechazados(ConsultaVerificacionRechazadosRequest $request){
        DB::beginTransaction();
        try{
            if($request->wantsJson()){
                $ImSolicitud = ImSolicitud::with([
                    "cat_status_verificacion",
                    "cat_status"
                ])
                ->where('id_oficina', $request->id_oficina)
                ->when(!empty($request->curp), function ($query) use ($request) {
                    $query->where("curp",$request->curp);
                })
                ->where("nombres", $request->nombres)
                ->where("primer_apellido", $request->primer_apellido)
                ->when(!empty($request->segundo_apellido), function ($query) use ($request) {
                    $query->where("segundo_apellido",$request->segundo_apellido);
                })
                ->where("fecha_nacimiento", $request->fecha_nacimiento)
                ->where("id_tipo_solicitud",3)
                ->where("bol_eliminado",false)
                ->where("id_estatus_solicitud",200)
                ->orderByDesc('created_at')
                ->get();

                dd($ImSolicitud);

                $results = [];
                $message = "";
                if( $ImSolicitud ){
                    $message = "Se encontro la solicitud de verificación rechazada con el id_solicitud ".$ImSolicitud->id_solicitud.".";
                    $results = [
                        'id_solicitud' => $ImSolicitud->id_solicitud,
                        'estatus_solicitud' => optional($ImSolicitud->cat_status)->estatus_solicitud,
                        'cuerpo_correo' => ( $ImSolicitud->cuerpo_correo ? $ImSolicitud->cuerpo_correo : '' ),
                        'estatus' => optional($ImSolicitud->cat_status_verificacion)->estatus ?? null,
                        'bol_estatus' => optional($ImSolicitud->cat_status_verificacion)->bol_estatus ?? null,
                    ];
                }else{
                    $message = "No encontraron solicitudes de verificación rechazadas.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'Solicitud' => $results,
                ], 200);

            }else{
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en get_cat_type_impediment', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'code'    => $e->getCode(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }

    public function transformar_en_mayusculas($data){
        try{

            $omitidos = ['foto', 'probatorio_nacionalidad', 'probatorio_identidad'];

            $data['correo_electronico'] = ImCatOficina::find($data["id_oficina"]);
            $data['correo_electronico'] = optional( $data['correo_electronico'] )->correo_electronico;

            return (object)collect($data)->map(function ($valor, $campo) use ($omitidos) {
                if (in_array($campo, $omitidos)) {
                    return $valor; // se deja igual
                }

                if( is_string($valor) ){
                    $valor = trim($valor);
                    $valor = preg_replace('/\s+/', ' ', $valor);
                    $valor = mb_strtoupper($valor, 'UTF-8');
                }

                return $valor;
            })->toArray();

        } catch (Exception $e) {
            return response()->json([
            'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }


    public function save_file_base64($imgbaseb64,$id){
        try{

            $response = [
                    'success' => false,
                    'message' => null,
                    'name' => null,
                    'relativePath' => null,
                    'absolutePath' => null,
                    'mime' => null,
                    'size_kb' => null,
            ];

            if ($imgbaseb64 && Storage::getDefaultDriver() === "nfs") {

                $disk = Storage::disk('nfs');
                $root = $disk->path('impedimentos/files/service');

                // Crear carpeta si no existe
                if (!File::isDirectory($root)) {
                    File::makeDirectory($root, 0775, true);
                }

                // Obtener base64
                $base64 = trim((string) $imgbaseb64);
                $base64 = str_replace(' ', '+', $base64);

                // Detectar tipo MIME desde el encabezado si existe
                $mime = null;
                if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+|application\/pdf);base64,/', $base64, $m)) {
                    $mime = strtolower($m[1]);
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                }

                // Decodificar base64
                $data = base64_decode($base64, true);
                if ($data === false) {
                    $response['success'] = false;
                    $response['message'] = 'No se pudo decodificar el archivo base64.';
                    return $response;
                    // throw new \RuntimeException('No se pudo decodificar el archivo base64.');
                }

                // Detectar MIME real (más confiable)
                if (function_exists('finfo_buffer')) {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $detectedMime = strtolower($finfo->buffer($data));
                    if (!empty($detectedMime)) {
                        $mime = $detectedMime;
                    }
                }

                // Validar tipos permitidos
                $permitidos = ['image/png', 'image/jpeg', 'application/pdf'];
                if (!in_array($mime, $permitidos, true)) {
                    $response['success'] = false;
                    $response['message'] = "Tipo de archivo no permitido: {$mime}";
                    return $response;
                    // throw new \RuntimeException("Tipo de archivo no permitido: {$mime}");
                }

                // Asignar extensión
                $ext = match ($mime) {
                    'image/png' => 'png',
                    'image/jpeg' => 'jpg',
                    'application/pdf' => 'pdf',
                    default => 'bin',
                };

                // Nombre de archivo
                $filename = 'id_solicitud_' . $id . '_' . time() . '.' . $ext;
                $response['name'] = $filename;
                $relativePath = 'impedimentos/files/service/' . $filename;

                // Guardar archivo
                $ok = $disk->put($relativePath, $data);
                if (!$ok) {
                    $response['success'] = false;
                    $response['message'] = 'No se pudo guardar el archivo en el disco NFS.';
                    return $response;
                    // throw new \RuntimeException('No se pudo guardar el archivo en el disco NFS.');
                }

                // Obtener ruta absoluta
                $absolutePath = $disk->path($relativePath);
                $response['success'] = true;
                $response['message'] = 'Se guardo el archivo exitosamente.';
                $response['relativePath'] = "/media-file/$relativePath";
                $response['absolutePath'] = $absolutePath;
                $response['mime'] = $mime;
                $response['size_kb'] = round(strlen($data) / 1024, 2);
            }

            return (object)$response;

        } catch (Exception $e) {
            return response()->json([
            'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line'    => $e->getline(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
            ], 300);
        }
    }



}
