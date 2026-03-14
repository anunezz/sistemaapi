<?php

namespace App\Traits;
use App\Models\ImSolicitud;
use App\Models\ImSolicitudCausal;
use App\Models\ImSolicitudDocumento;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\DischargeImpedimentsController;
use App\Models\ImImpedimento;
use App\Models\ImImpedimentoDocumento;
use App\Models\ImImpedimentoBaja;
use App\Models\ImPersonaPadre;
use App\Models\ImPersona;
use Nette\Utils\Json;

trait RequestImpedimentTrait
{
    private string $id_user;

    public function __construct()
    {
        $this->id_user = Auth::id();
    }

    public function update_request($request) {
        DB::beginTransaction();
        try{

            if( count( $request->delete_id_anexo ) > 0 ){
                DB::table('im_solicitud_documento')
                ->whereIn('id_solicitud_documento', $request->delete_id_anexo)
                ->update([
                    'bol_eliminado' => true,
                    'updated_at'    => now(), // si usas timestamps
                    "id_usuario_modificacion" => $this->id_user,
                ]);
            }

            $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
            if ($ImSolicitud->id_tipo_solicitud == 3) {
                $rows = collect($request->input('multiCausalSubcausal', []))
                    ->filter(fn ($p) => isset($p['id_causal_impedimento'], $p['id_subcausal_impedimento']))
                    ->map(fn ($p) => [
                        'solicitud_id'             => (int) $ImSolicitud->id_solicitud,
                        'id_causal_impedimento'    => (int) $p['id_causal_impedimento'],
                        'id_subcausal_impedimento' => (int) $p['id_subcausal_impedimento'],
                        'updated_at'               => now(),
                        'created_at'               => now(),
                    ])->values()->all();

                // MERGE: inserta si no existe, actualiza si ya existe. No borra nada.
                ImSolicitudCausal::query()->upsert(
                    $rows,
                    ['solicitud_id','id_causal_impedimento','id_subcausal_impedimento'],
                    ['updated_at'] // añade 'plantilla' aquí si la guardas en esta tabla
                );
            }

            $ImSolicitud->update([
                "id_tipo_solicitud" => $request->id_tipo_solicitud,
                "correo_electronico" => $request->correo_electronico,
                "motivacion_acto_juridico" => $request->motivacion_acto_juridico,
                "id_causal_impedimento" => $request->id_causal_impedimento,
                "causal_otro_descripcion" => $request->causal_otro_descripcion,
                "id_subcausal_impedimento" => $request->id_subcausal_impedimento,
                "id_oficina" => $request->id_oficina,
                "id_prioridad" => $request->id_prioridad,
                "numero_documento" => $request->numero_documento,
                "dependencia" => $request->dependencia,
                "nombre_dependencia" => $request->nombre_dependencia,
                "urgencia" => $request->urgencia,
                "nombres" => $request->nombres,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => Carbon::parse($request->fecha_nacimiento)->format("Y-m-d"),
                "entidad_federativa_nacimiento" => $request->entidad_federativa_nacimiento,
                "curp" => $request->curp,
                "id_genero" => $request->id_genero,
                "nombres_padre" => $request->padre_nombres,
                "primer_apellido_padre" => $request->padre_primer_apellido,
                "segundo_apellido_padre" => $request->padre_segundo_apellido,
                "nombres_madre" => $request->madre_nombres,
                "primer_apellido_madre" => $request->madre_primer_apellido,
                "segundo_apellido_madre" => $request->madre_segundo_apellido,
                "curp_identidad" => $request->curp_identidad,
                "nombres_identidad" => $request->nombres_identidad,
                "primer_apellido_identidad" => $request->primer_apellido_identidad,
                "segundo_apellido_identidad" => $request->segundo_apellido_identidad,
                "id_usuario_modificacion" => $this->id_user
            ]);

            $DischargeImpedimentsController = new DischargeImpedimentsController();

            foreach (collect($request->selection_anexo)->where("aux",true) as $item) {
                $newLocationPath = str_replace(['filesTemp'], 'files', $item["url_documento"]);

                if(  isset( $item["id_solicitud_documento"] ) ){
                    $ImSolicitudDocumento = ImSolicitudDocumento::find($item["id_solicitud_documento"]);
                    $ImSolicitudDocumento->update([
                        "identificador_documento" => $item["identificador_documento"],
                        "url_documento" => $newLocationPath,
                        "id_usuario_modificacion" => $this->id_user,
                        "observaciones" => $item["observaciones"]
                    ]);
                }else{
                    $ImSolicitudDocumento = ImSolicitudDocumento::create([
                        "id_solicitud" => $ImSolicitud->id_solicitud,
                        "id_cat_anexos" => $item["id_cat_anexos"],
                        "identificador_documento" => $item["identificador_documento"],
                        "url_documento" => $newLocationPath,
                        "id_usuario_alta" => $this->id_user,
                        "observaciones" => $item["observaciones"]
                    ]);
                }

                $DischargeImpedimentsController->moveFileLocation($newLocationPath,$item["url_documento"]);
            }

            DB::commit();

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

    public function update_impediment($request) {
        DB::beginTransaction();
        try{
            $ImSolicitud = ImSolicitud::with([
                'documents' => function ($query) {
                    $query->where('bol_eliminado', false);
                },
            ])->find(decrypt($request->hash_id));

            $ImImpedimento = ImImpedimento::with([
                'people.people_fathers',
                'documents' => function ($query) {
                    $query->where('bol_eliminado', false);
                }
            ])->find( $request->id_impedimento );

            foreach ($ImImpedimento->documents as $document) {
                $document->bol_eliminado = true;
                $document->id_usuario_modificacion = $this->id_user;
                $document->save();
            }

            foreach ($ImSolicitud->documents as $item) {
                $item = collect($item)->toArray();
                $item["id_impedimento"] = $ImImpedimento->id_impedimento;
                ImImpedimentoDocumento::create($item);
            }

            $ImImpedimento->update([
                "id_oficina" => $request->id_oficina,
                "dependencia" => $request->dependencia,
                "nombre_dependencia" => $request->nombre_dependencia,
                "correo_electronico" => $request->correo_electronico,
                "motivacion_acto_juridico" => $request->motivacion_acto_juridico,
                "id_causal_impedimento" => $request->id_causal_impedimento,
                "causal_otro_descripcion" => $request->causal_otro_descripcion,
                "id_subcausal_impedimento" => $request->id_subcausal_impedimento,
                "id_usuario_modificacion" => $this->id_user
            ]);

            $data_people = [
                "nombres" => $request->nombres,
                "primer_apellido" => $request->primer_apellido,
                "segundo_apellido" => $request->segundo_apellido,
                "fecha_nacimiento" => Carbon::parse($request->fecha_nacimiento)->format("Y-m-d"),
                "entidad_federativa_nacimiento" => $request->entidad_federativa_nacimiento,
                "id_usuario_modificacion" => $this->id_user,
                "id_genero" => $request->id_genero,
            ];

            if( $request->curp ){
                $data_people['curp'] = $request->curp;
            }

            $ImImpedimento->people()->update($data_people);

            $ImImpedimento->people->people_fathers()->update([
                "nombres_padre" => $request->padre_nombres,
                "primer_apellido_padre" => $request->padre_primer_apellido,
                "segundo_apellido_padre" => $request->padre_segundo_apellido,
                "nombres_madre" => $request->madre_nombres,
                "primer_apellido_madre" => $request->madre_primer_apellido,
                "segundo_apellido_madre" => $request->madre_segundo_apellido,
                "id_usuario_modificacion" => $this->id_user
            ]);

            DB::commit();
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

    public function nuevo_bol_eliminado_impediment($backup_anterior,$ImSolicitud) {
        DB::beginTransaction();
        try{
            DB::table('im_impedimentos_solicitudes')
            ->where('id_solicitud', $ImSolicitud->id_solicitud)
            ->where('id_impedimento', $backup_anterior->id_impedimento)
            ->delete();
            ImImpedimentoBaja::where('id_impedimento', $backup_anterior->id_impedimento)->update(['bol_eliminado' => true]);
            ImImpedimentoDocumento::where('id_impedimento', $backup_anterior->id_impedimento)->update(['bol_eliminado' => true]);
            ImImpedimento::where('id_impedimento', $backup_anterior->id_impedimento)->update(['bol_eliminado' => true]);
            ImPersonaPadre::where('id_persona', $backup_anterior->people->id_persona)->update(['bol_eliminado' => true]);
            ImPersona::where('id_persona', $backup_anterior->people->id_persona)->update(['bol_eliminado' => true]);

            DB::commit();
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

    public function backup_solicitud($id_solicitud) {
        DB::beginTransaction();
        try{
            $ImSolicitud = ImSolicitud::with([
                    'impediment'
                ])->find($id_solicitud);

            $backup = collect( json_decode($ImSolicitud->backup_anterior))->toArray();
            $ImSolicitud->fill( $backup );

            ImSolicitudDocumento::where("id_solicitud",$id_solicitud)->update(['bol_eliminado' => true]);

            $backup = json_decode(  $ImSolicitud->backup_anterior );
            foreach ($backup->documents as $document) {
                $ImSolicitudDocumento = ImSolicitudDocumento::find( $document->id_solicitud_documento );
                $ImSolicitudDocumento->fill( collect($document)->toArray() );
                $ImSolicitudDocumento->save();
            }

            $ImSolicitud->backup_anterior = null;
            $ImSolicitud->save();
            DB::commit();
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

    public function backup_impedimento($id_solicitud,$id_impedimento) {
        DB::beginTransaction();
        try{
            DB::table('im_impedimentos_solicitudes')
            ->where('id_solicitud', $id_solicitud)
            ->where('id_impedimento', $id_impedimento)
            ->delete();

            $ImImpedimento = ImImpedimento::find($id_impedimento);

            if($ImImpedimento && $ImImpedimento->backup_anterior ){
                $backup_impedimento = collect(json_decode($ImImpedimento->backup_anterior))->toArray();
                $ImImpedimento->fill($backup_impedimento);
                $ImImpedimento->save();

                if( isset($backup_impedimento["people"]) && $backup_impedimento["people"]){
                    $people_backup = collect( $backup_impedimento["people"] )->toArray();
                    $ImPersona = ImPersona::find( $people_backup["id_persona"] );
                    if( $ImPersona ){
                        $ImPersona->fill($people_backup);
                        $ImPersona->save();
                    }
                }

                if( isset($backup_impedimento["fathers"]) && $backup_impedimento["fathers"] ){
                    $fathers_backup = collect( $backup_impedimento["fathers"] )->toArray();
                    $ImPersonaPadre = ImPersonaPadre::find( $fathers_backup["id_persona"] );
                    if( $ImPersonaPadre ){
                        $ImPersonaPadre->fill($fathers_backup);
                        $ImPersonaPadre->save();
                    }
                }

                if( isset($backup_impedimento["low"]) && $backup_impedimento["low"] ){
                    $low_backup = collect( $backup_impedimento["low"] )->toArray();
                    $ImImpedimentoBaja = ImImpedimentoBaja::find( $low_backup["id_secuencial_baja"] );
                    if( $ImImpedimentoBaja ){
                        $ImImpedimentoBaja->fill($low_backup);
                        $ImImpedimentoBaja->save();
                    }
                }

                if( isset($backup_impedimento["documents"]) && $backup_impedimento["documents"] && count( $backup_impedimento["documents"] ) > 0 ){
                    ImImpedimentoDocumento::where('id_impedimento', $ImImpedimento->id_impedimento)->update(['bol_eliminado' => true]);
                    foreach ($backup_impedimento["documents"] as $doc) {
                        $document_update = ImImpedimentoDocumento::find($doc->id_impedimento_documento);
                        $document_update->fill(collect($doc)->toArray());
                        $document_update->save();
                    }
                }
            }

            DB::commit();
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

}
