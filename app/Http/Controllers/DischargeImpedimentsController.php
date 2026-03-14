<?php

namespace App\Http\Controllers;

use App\Mail\VerificationResultMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Catalogs\ImCatEstatusSolicitud;
use App\Models\Catalogs\ImCatEstatusVerificacion;
use App\Models\ImAsignacionSolicitudes;
use App\Models\ImImpedimentoBitacora;
use App\Models\ImPersonaBitacora;
use App\Models\ImSolicitudBitacora;
use App\Models\User;
use App\Traits\BinnacleTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Catalogs\ImCatOficina;
use App\Models\ImPersona;
use App\Models\ImSolicitud;
use App\Models\Catalogs\ImCatCausalImpedimento;
use App\Models\ImPersonaPadre;
use App\Models\ImCatAnexos;
use App\Traits\validFile;
use App\Traits\EscapeTextTrait;
use App\Traits\MoveFileToFinalLocationTrait;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use App\Models\ImCatTipoSolicitud;
use App\Models\ImImpedimento;
use App\Models\ImImpedimentoBaja;
use App\Models\Catalogs\ImCatPrioridades;
use App\Services\TransactionService;
use App\Models\ImSolicitudDocumento;
use App\Models\ImImpedimentoDocumento;
use App\Traits\SnapshotTrait;
use App\Mail\MailImpediments;
use App\Mail\MailImpedimentsHigh;
use App\Mail\MailImpedimentsLow;
use App\Traits\RequestImpedimentTrait;
use App\Models\Catalogs\ImCatSubCausalImpedimento;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Catalogs\ImCatGeneralGenero;
use App\Models\Transaccion;
use App\Traits\BinnacleImpedimentTrait;

class DischargeImpedimentsController extends Controller
{
    use validFile, EscapeTextTrait, SnapshotTrait, BinnacleTrait, MoveFileToFinalLocationTrait, RequestImpedimentTrait,BinnacleImpedimentTrait;
    private string $id_user;

    public function __construct()
    {
        $this->id_user = Auth::id();
    }

    public function get_cat_type_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                return response()->json([
                    'success' => true,
                    'Results' => ImCatTipoImpedimento::get()
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_cats(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "cat_oficinas" => ImCatOficina::porTipoActivas(2)->orderBy('cad_oficina', 'asc')->where('bol_eliminado', false)->get(),
                        "cat_prioridades" => ImCatPrioridades::orderBy('prioridad', 'asc')->get(),
                        "causal_impedimento" => ImCatCausalImpedimento::orderBy('causal_impedimento', 'asc')->with([
                            'cat_subcausal_impedimento' => function ($q) {
                                $q->where('bol_eliminado', false)
                                    ->with('cat_plantilla');
                            }
                        ])->when(isset($request->id_tipo_solicitud) && $request->id_tipo_solicitud != null, function ($query) use ($request) {
                            $causal_bajas = collect([10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
                            switch ($request->id_tipo_solicitud) {
                                case 4:
                                    $query->where('id_causal_impedimento', 9);
                                    break;
                                case 2:
                                    $query->whereIn('id_causal_impedimento', $causal_bajas);
                                    break;
                                default:
                                    $causal_bajas->push(9);
                                    $query->whereNotIn('id_causal_impedimento', $causal_bajas);
                                    break;
                            }
                            return $query->where('bol_eliminado', false);
                        })
                            ->orderBy('causal_impedimento', 'asc')
                            ->get(),
                        "cat_anexos" => ImCatAnexos::orderBy('nombre', 'asc')->with([
                            'types_of_requests'
                        ])
                            ->whereHas('types_of_requests', function ($q) use ($request) {
                                return $q->where('im_cat_tipo_solicitud.id_tipo_solicitud', $request->id_tipo_solicitud);
                            })
                            ->get(),
                        "cat_tipo_solicitud" => ImCatTipoSolicitud::orderBy('tipo_solicitud', 'asc')->get(),
                        "cat_estatus_verificacion" => ImCatEstatusVerificacion::get()
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }
    public function get_cats_impediment()
    {
        try {
            return response()->json([
                'success' => true,
                'Results' => [
                    "cat_oficinas" => ImCatOficina::porTipoActivas(2)->orderBy('cad_oficina', 'asc')->get(),
                    "cat_prioridades" => ImCatPrioridades::orderBy('prioridad', 'asc')->get(),
                    "causal_impedimento" => ImCatCausalImpedimento::orderBy('causal_impedimento', 'asc')->with([
                        'cat_subcausal_impedimento'
                    ])->get()
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();

                $data_request = [
                    "id_tipo_solicitud" => $request->id_tipo_solicitud,
                    "fecha_registro" => Carbon::parse($request->fecha_registro)->format("Y-m-d"),
                    "correo_electronico" => $request->correo_electronico,
                    "motivacion_acto_juridico" => $request->motivacion_acto_juridico,
                    "id_estatus_solicitud" => 10,
                    "id_causal_impedimento" => $request->id_causal_impedimento,
                    "causal_otro_descripcion" => $request->causal_otro_descripcion,
                    "id_subcausal_impedimento" => $request->id_subcausal_impedimento,
                    "id_oficina" => $request->id_oficina,
                    "id_prioridad" => $request->id_prioridad,
                    "numero_documento" => $request->numero_documento,
                    "dependencia" => $request->dependencia,
                    "nombre_dependencia" => $request->nombre_dependencia,
                    "urgencia" => $request->urgencia,
                    "id_usuario_alta" => $this->id_user,
                    "nombres" => $request->nombres,
                    "primer_apellido" => $request->primer_apellido,
                    "segundo_apellido" => $request->segundo_apellido,
                    "fecha_nacimiento" => ($request->fecha_nacimiento ? Carbon::parse($request->fecha_nacimiento)->format("Y-m-d") : null),
                    "entidad_federativa_nacimiento" => $request->entidad_federativa_nacimiento,
                    "persona_correo_electronico" => $request->persona_correo_electronico,
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
                    "id_usuario_modificacion" => $this->id_user,
                    "id_usuario_elaboro" => $this->id_user
                ];

                if ($request->id_tipo_solicitud == 3) {
                    $data_request["id_estatus_verificacion"] = 1;
                }

                $ImSolicitud = ImSolicitud::create($data_request);

                foreach ($request->selection_anexo as $item) {
                    $newLocationPath = str_replace(['filesTemp'], 'files', $item["url_documento"]);

                    $ImSolicitudDocumento = ImSolicitudDocumento::create([
                        "id_solicitud" => $ImSolicitud->id_solicitud,
                        "id_cat_anexos" => $item["id_cat_anexos"],
                        "identificador_documento" => $item["identificador_documento"],
                        "url_documento" => $newLocationPath,
                        "id_usuario_alta" => $this->id_user,
                        "observaciones" => $item["observaciones"]
                    ]);

                    $this->moveFileLocation($newLocationPath, $item["url_documento"]);
                }

                $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 3,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "Se creo la solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", con el estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function rejection_validates(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $ImSolicitud->id_estatus_solicitud = 300;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->backup_anterior = json_encode(["action" => "validacion"]);
                $ImSolicitud->save();

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . " cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);

                DB::commit();

                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function reject_cancel(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::with([
                    'impedimento' => function ($query) {
                        $query->with([
                            'low' => function ($q) {
                                $q->where('bol_eliminado', false);
                            },
                        ])->where('bol_eliminado', false);
                    }
                ])->find(decrypt($request->hash_id));

                $status = 40;

                if ($ImSolicitud->backup_anterior) {
                    $backup_solicitud = json_decode($ImSolicitud->backup_anterior);
                    switch ($backup_solicitud->action) {
                        case 'nuevo':
                            $this->backup_solicitud($ImSolicitud->id_solicitud);
                            break;
                        case 'validacion':
                            $status = 40;
                        break;
                    }
                }

                $ImSolicitud->id_estatus_solicitud = $status;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->save();

                $ImSolicitud = ImSolicitud::with([
                    'impediment' => function ($query) {
                        $query->where('bol_eliminado', false);
                    }
                ])->find(decrypt($request->hash_id));

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);
                $aux = true;
                if ($ImSolicitud->impedimento()->exists()) {
                    $impediment = $ImSolicitud->impedimento;
                    $aux = false;
                    $backup_impediment = json_decode($impediment->backup_anterior);
                    switch ($backup_impediment->action) {
                        case 'nuevo':
                            $this->nuevo_bol_eliminado_impediment($backup_impediment, $ImSolicitud);
                            break;
                        case 'validacion':
                            $status = 40;
                            break;
                        case 'existe_impedimento':
                            $this->backup_impedimento($ImSolicitud->id_solicitud, $impediment->id_impedimento);
                            $ImImpedimento = ImImpedimento::find($impediment->id_impedimento);
                            $ImImpedimento->id_estatus_impedimento = 100;
                            $ImImpedimento->save();
                            if ($ImImpedimento->low) {
                                $ImImpedimento->low->bol_eliminado = true;
                                $ImImpedimento->low->id_estatus_impedimento_baja = 100;
                                $ImImpedimento->low->save();
                            }
                            //TODO AQUI VA IR TU BITACORA bitacora de impedimentos
                            $snapshot = $this->guardarSnapshot($ImImpedimento, \App\Models\ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $snapshot->tipo_solicitud]);

                            Transaccion::create([
                                "user_id" => $this->id_user,
                                "cat_transaction_type_id" => 2,
                                "cat_module_id" => $request->moduleId,
                                "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                                ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImImpedimento->id_impedimento.
                                " se actualizó y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                            ]);
                        break;
                    }
                }

                if( $aux == true){
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 2,
                        "cat_module_id" => $request->moduleId,
                        "action"=> "La solicitud el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                    ]);
                }

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function reject_authorize(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::with([
                    'impedimento' => function ($query) {
                        $query->with(
                            [
                                'low' => function ($q) {
                                    $q->where('bol_eliminado', false);
                                }
                            ]
                        )->where('bol_eliminado', false);
                    },
                ])->find(decrypt($request->hash_id));

                $ImSolicitud->id_estatus_solicitud = 200;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->save();

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);
                $aux = true;
                if ($ImSolicitud->impedimento()->exists()) {
                    $impediment = $ImSolicitud->impedimento;
                    $aux = false;
                    $backup_impediment = json_decode($impediment->backup_anterior);
                    switch ($backup_impediment->action) {
                        case 'nuevo':
                            $this->nuevo_bol_eliminado_impediment($backup_impediment, $ImSolicitud);
                            break;
                        case 'validacion':
                            $status = 40;
                            break;
                        case 'existe_impedimento':
                            $this->backup_impedimento($ImSolicitud->id_solicitud, $impediment->id_impedimento);
                            $ImImpedimento = ImImpedimento::find($impediment->id_impedimento);
                            $ImImpedimento->id_estatus_impedimento = 100;
                            $ImImpedimento->save();
                            if ($ImImpedimento->low()->exists()) {
                                $ImImpedimentoBaja = ImImpedimentoBaja::find($ImImpedimento->low->id_secuencial_baja);
                                if ($ImImpedimentoBaja) {
                                    $ImImpedimentoBaja->bol_eliminado = true;
                                    $ImImpedimentoBaja->id_estatus_impedimento_baja = 100;
                                    $ImImpedimentoBaja->save();
                                }
                            }

                            //TODO AQUI VA IR TU BITACORA bitacora de impedimentos
                            $snapshot = $this->guardarSnapshot($ImImpedimento, \App\Models\ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $snapshot->tipo_solicitud]);

                            Transaccion::create([
                                "user_id" => $this->id_user,
                                "cat_transaction_type_id" => 2,
                                "cat_module_id" => $request->moduleId,
                                "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                                ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImImpedimento->id_impedimento.
                                " se actualizó y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                            ]);
                            break;
                    }
                }

                if( $aux == true){
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 2,
                        "cat_module_id" => $request->moduleId,
                        "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                    ]);
                }

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function reject_authorization_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::with([
                    'impediment' => function ($query) {
                        $query->with(
                            [
                                'low' => function ($q) {
                                    $q->where('bol_eliminado', false);
                                }
                            ]
                        )->where('bol_eliminado', false);
                    },
                    'impedimento' => function ($query) {
                        $query->with(
                            [
                                'low' => function ($q) {
                                    $q->where('bol_eliminado', false);
                                }
                            ]
                        )->where('bol_eliminado', false);
                    },
                ])->find(decrypt($request->hash_id));

                $ImSolicitud->id_estatus_solicitud = 200;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->save();

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);
                $aux = true;
                if ($ImSolicitud->impedimento()->exists()) {
                    $this->backup_impedimento($ImSolicitud->id_solicitud, $ImSolicitud->impedimento->id_impedimento);
                    $aux = false;
                    $ImImpedimento = ImImpedimento::find($ImSolicitud->impedimento->id_impedimento);
                    if ($ImImpedimento->lows()->exists()) {
                        foreach ($ImImpedimento->lows()->get() as $low) {
                            $low->bol_eliminado = true;
                            $low->id_estatus_impedimento_baja = 100;
                            $low->save();
                        }
                    }

                    //TODO AQUI VA IR TU BITACORA bitacora de impedimentos
                    //$snapshot = $this->guardarSnapshot($ImImpedimento, \App\Models\ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $snapshot->tipo_solicitud]);
                    self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                    }

                if ($ImSolicitud->impedimento()->exists()) {
                    $ImImpedimento = ImImpedimento::find($ImSolicitud->impedimento->id_impedimento);
                    //$this->guardarSnapshot($ImImpedimento, \App\Models\ImImpedimentoBitacora::class, [], ['tipo_solicitud', $snapshot->tipo_solicitud]);
                    self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 2,
                        "cat_module_id" => $request->moduleId,
                        "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                        ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImImpedimento->id_impedimento.
                        " se actualizó y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                    ]);
                }

                if( $aux == true){
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 2,
                        "cat_module_id" => $request->moduleId,
                        "action"=> "La solicitud el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                    ]);
                }

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function reject_response_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::with([
                    'impediment' => function ($query) {
                        $query->with([
                            'low' => function ($q) {
                                $q->where('bol_eliminado', false);
                            },
                        ])->where('bol_eliminado', false);
                    },
                    'impedimento' => function ($query) {
                        $query->with([
                            'low' => function ($q) {
                                $q->where('bol_eliminado', false);
                            },
                        ])->where('bol_eliminado', false);
                    }
                ])->find(decrypt($request->hash_id));

                $ImSolicitud->id_estatus_solicitud = 200;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->save();

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);
                $this->guardarMovimiento($this->id_user, $request->moduleId, 2, 'Se cambió el estatus a "' . $snapshot->estatus_solicitud . '" de una solicitud con el ID ' . $ImSolicitud->id_solicitud);

                if ($ImSolicitud->impedimento()->exists()) {
                    $backup_impediment = json_decode($ImSolicitud->impedimento->backup_anterior);
                    switch ($backup_impediment->action) {
                        case 'nuevo':
                            $this->nuevo_bol_eliminado_impediment($backup_impediment, $ImSolicitud);
                            break;
                        case 'validacion':
                            $status = 40;
                            break;
                        case 'existe_impedimento':
                            $this->backup_impedimento($ImSolicitud->id_solicitud, $ImSolicitud->impedimento->id_impedimento);
                            $ImImpedimento = ImImpedimento::find($ImSolicitud->impedimento->id_impedimento);
                            if ($ImImpedimento->lows()->exists()) {
                                foreach ($ImImpedimento->lows()->get() as $low) {
                                    $low->bol_eliminado = true;
                                    $low->id_estatus_impedimento_baja = 100;
                                    $low->save();
                                }
                            }

                            //TODO AQUI VA IR TU BITACORA bitacora de impedimentos
                            $snapshot = $this->guardarSnapshot($ImImpedimento, \App\Models\ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $snapshot->tipo_solicitud]);
                        break;
                        default:
                            dd("No hay valor");
                        break;
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'Results' => [],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function send_to_pending(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $ImSolicitud->id_estatus_solicitud = 30;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->save();

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó, se cambio el estatus a " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'Results' => [],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    //TODO FUNCION PENDIOENTE DE ELIMINAR
    public function send_to_for_rejecting(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $ImSolicitud->id_estatus_solicitud = 300;
                $ImSolicitud->observaciones = $request->observaciones;
                $ImSolicitud->backup_anterior = json_encode(["action" => "nuevo"]);
                $ImSolicitud->save();

                //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );

                //------- GUARDAR MOVIMIENTO EN BITACORA GENERAL -----
                $this->guardarMovimiento($this->id_user, $request->moduleId, 2, 'Se cambió el estatus a "' . $snapshot->estatus_solicitud . '" de una solicitud con el ID ' . $ImSolicitud->id_solicitud);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'Results' => [],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $filters = (object) $request->filters;
                $user = auth()->user();

                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_priority",
                            "cat_office",
                            "cat_status",
                            "cat_type",
                            "cat_causal_impedimento"
                        ])
                            ->whereIn('id_estatus_solicitud', [10, 30])
                            ->whereDoesntHave('impediment')
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 2, function ($query) use ($user) {
                                return $query->where("id_oficina", $user->id_oficina);
                            })
                            ->when($user->usuarioPerfil->id_perfil == 3, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }
    public function get_data_inbox_authorization_requests(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                $user = auth()->user();
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_type",
                            "cat_causal_impedimento"
                        ])
                            ->where("id_estatus_solicitud", 20)
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 2, function ($query) use ($user) {
                                return $query->where("id_oficina", $user->id_oficina);
                            })
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 3, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }
    public function get_data_inbox_authorization_rejection(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                $user = auth()->user();
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_type",
                            "cat_causal_impedimento",
                            "impediment" => function ($query) {
                                $query->with([
                                    'cat_status',
                                    'low' => function ($q) {
                                        $q->with([
                                            'cat_status'
                                        ])->where('bol_eliminado', false);
                                    }
                                ])->where('bol_eliminado', false);
                            },
                        ])
                            // ->whereIn("id_tipo_solicitud",[1,2,4])
                            ->where("id_estatus_solicitud", 300)
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil != 4, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }
    public function get_data_inbox_validate_high(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;

                $user = auth()->user();

                $solicitudes = ImSolicitud::with([
                    "cat_office",
                    "cat_status",
                    "cat_causal_impedimento",
                    "cat_type",
                ])
                    ->where("id_estatus_solicitud", 40)
                    ->where("id_tipo_solicitud", 1)
                    ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                    ->orderByDesc('created_at')
                    ->search($filters)
                    ->when($user->usuarioPerfil->id_perfil == 3, function ($query) use ($user) {
                        return $query->whereHas('asignaciones', function ($q) {
                            $q->where('id_usuario', auth()->id());
                        });
                    })
                    ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 2, function ($query) {
                        return $query->whereRaw('1 = 0');
                    })
                    ->paginate($request->rowsPerPage);

                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => $solicitudes
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }
    public function get_data_inbox_work_assignation(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                $only_assigned = $request->only_assigned;
                $user = auth()->user();
                //dd( $filters );
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "usuarios" => User::with('usuarioPerfil')->orderBy('username', 'asc')->get(),
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_causal_impedimento",
                            "cat_type",
                            "asignacion.usuario"
                        ])
                            ->where("id_estatus_solicitud", 40)
                            ->orderByRaw("
                                CASE
                                    WHEN urgencia IS TRUE THEN 0
                                    WHEN id_prioridad = 1 THEN 1
                                    WHEN id_prioridad = 2 THEN 2
                                    WHEN id_prioridad = 3 THEN 3
                                END
                            ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 2 || $user->usuarioPerfil->id_perfil == 3, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->when(
                                $only_assigned,
                                fn($query) => $query->onlyAssignedRequests(),
                                fn($query) => $query->onlyAvailableRequests()
                            )
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_inbox_verification(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                $user = auth()->user();
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_causal_impedimento",
                            "cat_type",
                            "causales.causal"
                        ])
                            ->where(function ($q) use ($user) {
                                if (in_array($user->usuarioPerfil->id_perfil, [4, 5])) {
                                    $q->whereIn('id_estatus_solicitud', [40, 250]);
                                } else {
                                    $q->where('id_estatus_solicitud', 40);
                                }
                            })
                            ->where("id_tipo_solicitud", 3)
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 3, function ($query) use ($user) {
                                return $query->whereHas('asignaciones', function ($q) {
                                    $q->where('id_usuario', auth()->id());
                                });
                            })
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 2, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_inbox_validate_high_modify(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                $user = auth()->user();
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_causal_impedimento",
                            "cat_type",
                        ])
                            ->where("id_estatus_solicitud", 40)
                            ->where("id_tipo_solicitud", 4)
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 3, function ($query) use ($user) {
                                return $query->whereHas('asignaciones', function ($q) {
                                    $q->where('id_usuario', auth()->id());
                                });
                            })
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 2, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_authorization_high_impediments(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "impediment.cat_status",
                            "cat_causal_impedimento",
                            "cat_type",
                        ])
                            ->whereIn("id_estatus_solicitud", [50])
                            ->where("id_tipo_solicitud", 1)
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->restrictByPerfilOficina()
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_authorization_low_impediments(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $filters = (object) $request->filters;
                $user = auth()->user();
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "impedimento.cat_status",
                            "impedimento.low" => function ($query) {
                                $query->with(['cat_status'])->where('bol_eliminado', false);
                            },
                            "cat_causal_impedimento",
                            "cat_type",

                        ])
                            ->when($user->usuarioPerfil->id_perfil, function ($query) use ($user,$filters) {
                                switch ($user->usuarioPerfil->id_perfil) {
                                    case 4:
                                        $query->where("id_estatus_solicitud", 50);
                                        break;
                                    case 5:
                                        if( isset($filters->id_estatus) && count($filters->id_estatus) > 0 ){
                                            $query->whereIn("id_estatus_solicitud", $filters->id_estatus);
                                        }else{
                                            $query->whereIn("id_estatus_solicitud", [50, 250]);
                                        }
                                        break;
                                    default:
                                        $query->whereRaw('1 = 0');
                                        break;
                                }
                                return $query;
                            })
                            ->where("id_tipo_solicitud", 2)
                            ->search($filters)
                            ->restrictByPerfilOficina()
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                        ->orderByDesc('created_at')
                        ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_response_impediments(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $filters = (object) $request->filters;
                $user = auth()->user();

               // dd(8989);

                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_causal_impedimento",
                            "impedimento" => function ($query) {
                                $query->with([
                                    'cat_status',
                                    'low' => function ($q) {
                                        $q->with([
                                            'cat_status'
                                        ])
                                            //->where("id_estatus_impedimento_baja",100)
                                            ->where('bol_eliminado', false);
                                    }
                                ])->where('bol_eliminado', false);
                            },
                            "cat_type",
                        ])
                            ->whereIn("id_estatus_solicitud", [100, 150, 200, 1000])
                            // ->whereHas('impediment')
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 2, function ($query) use ($user) {
                                return $query->where("id_oficina", $user->id_oficina);
                            })
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 3, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_consult_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->wantsJson()) {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }

            // Toma filtros (o arreglo vacío si no vinieran)
            $filters = (object) ($request->filters ?? []);

            // Considera vacío si: null, '', o array vacío
            $allEmpty = collect((array) $filters)->every(function ($v) {
                return is_null($v) || $v === '' || (is_array($v) && count($v) === 0);
            });

            if ($allEmpty) {
                // Devuelve Paginador vacío (misma forma que el front espera)
                $emptyPaginator = ImImpedimento::whereRaw('1=0')
                    ->paginate($request->rowsPerPage ?? 10);

                return response()->json([
                    'success' => true,
                    'Results' => [
                        'ImImpedimento' => $emptyPaginator
                    ],
                ], 200);
            }

            // Normaliza arrays para selects múltiples (por si viene un solo int)
            // OJO: sólo si esos campos existen en tus filtros
            if (property_exists($filters, 'id_causal_impedimento')) {
                $filters->id_causal_impedimento = array_values(array_filter(
                    Arr::wrap($filters->id_causal_impedimento),
                    fn($v) => $v !== null && $v !== ''
                ));
            }
            if (property_exists($filters, 'id_oficina')) {
                $filters->id_oficina = array_values(array_filter(
                    Arr::wrap($filters->id_oficina),
                    fn($v) => $v !== null && $v !== ''
                ));
            }

            // Query normal
            $query = ImImpedimento::with([
                'cat_office',
                'people',
                'cat_type',
                'cat_status',
                'cat_causal',
                'cat_subcausal',
            ])
                ->whereIn('id_estatus_impedimento', [100, 150])
                ->where('bol_eliminado', false)
                ->orderByDesc('created_at')
                ->search($filters); // scope personalizado

            return response()->json([
                'success' => true,
                'Results' => [
                    'ImImpedimento' => $query->paginate($request->rowsPerPage ?? 10)
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }


    public function get_only_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                return response()->json([
                    'success' => true,
                    'Results' => ImImpedimento::with([
                        "cat_office",
                        "people.people_fathers",
                        "cat_type",
                        "cat_status",
                        "cat_causal",
                        "cat_subcausal.cat_plantilla",
                        "documents" => function ($query) {
                            $query->with(["cat_anexo"])->where('bol_eliminado', false);
                        }
                    ])
                        ->find(decrypt($request->hash_id)),
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_data_inbox_validate_low(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                $data = $request->all();
                $filters = (object) $request->filters;
                $user = auth()->user();
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "ImSolicitud" => ImSolicitud::with([
                            "cat_office",
                            "cat_status",
                            "cat_causal_impedimento",
                            "cat_type",
                        ])
                            ->where("id_estatus_solicitud", 40)
                            ->where("id_tipo_solicitud", 2)
                            ->orderByRaw("
                            CASE
                                WHEN urgencia IS TRUE THEN 0
                                WHEN id_prioridad = 1 THEN 1
                                WHEN id_prioridad = 2 THEN 2
                                WHEN id_prioridad = 3 THEN 3
                            END
                        ")
                            ->orderByDesc('created_at')
                            ->search($filters)
                            ->when($user->usuarioPerfil->id_perfil == 3, function ($query) use ($user) {
                                return $query->whereHas('asignaciones', function ($q) {
                                    $q->where('id_usuario', auth()->id());
                                });
                            })
                            ->when($user->usuarioPerfil->id_perfil == 1 || $user->usuarioPerfil->id_perfil == 2, function ($query) {
                                return $query->whereRaw('1 = 0');
                            })
                            ->paginate($request->rowsPerPage)
                    ],
                ], 200);


            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }


    public function upload_file(Request $request)
    {

        dd('se creo la ruta upload/file-chunks para subir archivos en partes');
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                //dd($request->all());

                $file = $request->file('file');
                $offset = $request->input('offset');
                $file_size = $request->input('file_size');

                $data = $request->all();

                $request->merge([
                    'file_name' => $this->escapeText($request->get('file_name'))
                ]);

                $fileName = $request->get('file_name');

                $fileName = $file_size . "_" . $fileName;

                $user_id = auth()->user()->id;

                $directory_cunks = $request->input('save_storage_cunks_folder');

                $directory = "$directory_cunks/$user_id";

                $path = "$directory/$fileName";

                if ($request->has('full_load') && $request->boolean('full_load')) {
                    $response = $this->moveFile($request, $path, $fileName);
                    return response()->json($response, 200);
                }

                if (Storage::disk('local')->exists($path) && ($offset === 0 || $offset === "0")) {
                    $size = Storage::disk('local')->size($path);
                    if ($this->formatSizeUnits((int) $file_size) === $this->formatSizeUnits((int) $size)) {
                        $response = $this->moveFile($request, $path, $fileName);

                        return response()->json($response, 200);
                    }
                    return response()->json([
                        'success' => true,
                        'file_exist' => true,
                        'size' => $size
                    ]);

                } else {
                    Storage::disk('local')->makeDirectory($directory);
                }

                //dd($path);

                $fp = fopen(storage_path("app/$path"), 'a+');
                fseek($fp, $offset);
                fwrite($fp, $file->get());
                fclose($fp);

                if ($request->has('is_last') && $request->boolean('is_last')) {
                    $response = $this->moveFile($request, $path, $fileName);

                    if ($response["success"] == false) {
                        return response()->json($response, 200);
                    }


                    if ($data["type_file_back_system"] == 1 || $data["type_file_back_system"] == 2 || $data["type_file_back_system"] == 3) {
                        $register = PhysicalLiftingsDocuments::create([
                            "id_cat_file_type" => $data["type_file_back_system"],
                            "path" => $response["path"],
                            "file_name" => $response["fileName"]
                        ]);
                    }

                    if (
                        $data["type_file_back_system"] == 4 ||
                        $data["type_file_back_system"] == 5 ||
                        $data["type_file_back_system"] == 6 ||
                        $data["type_file_back_system"] == 7
                    ) {
                        $register = PhysicalLiftingRmeUaDocuments::create([
                            "id_cat_file_type" => $data["type_file_back_system"],
                            "path" => $response["path"],
                            "file_name" => $response["fileName"]
                        ]);
                    }

                    $response["file_name"] = $register->file_name;
                    $response["id"] = $register->id;
                    $response["url"] = $register->url;
                    $response["id_cat_file_type"] = $register->id_cat_file_type;
                    DB::commit();
                    return response()->json($response, 200);
                }

                return response()->json([
                    'success' => true,
                ]);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'trace' => $e->getTrace()
            ], 300);
        }
    }

    public function moveFile($request, $path, $fileName)
    {

        $system_file_types = null;
        if ($request->has('type_file_back_system')) {
            $system_file_types = $request->integer('type_file_back_system');
        }

        $mimeType = $request->input('mimeType');
        $user_id = auth()->user()->id;


        if ($mimeType !== null) {
            Storage::disk('local')->append($path, "Content-Type: {$mimeType}\n\n");
        }

        ////obtener el metadata del archivo y validar si el archivo es valido
        $file = new \Illuminate\Http\UploadedFile(storage_path("app/$path"), $fileName);
        $name = $file->getFilename();
        $size = $file->getSize();
        $extension = $file->getExtension();

        $valid = $this->validateFile($file, $system_file_types);

        if (!$valid['typeFile']) {
            return [
                'success' => false,
                'message' => "Tipo de Archivo no valido"
            ];
        } else if (!$valid['maxSize']) {
            return [
                'success' => false,
                'message' => "El archivo no debe superar los 5MB"
            ];
        }
        ////////////////////////////////
        /// Crear nuevo nombre con hash
        $today = date("Y-m-d");
        $fileNameHash = $today . '_' . $user_id . '_' . Str::random(40) . '.' . $extension;



        $newPath = $request->input('save_storage_folder');
        $newPath = "$newPath/$user_id/$fileNameHash"; //path final definido en el frontend para moverlo a la ruta final

        Storage::disk('local')->move($path, $newPath);

        return [
            'file_fully_uploaded' => true,
            'success' => true,
            'fileName' => $request->get('file_name'),
            'fileNameHash' => $fileNameHash,
            'file_location' => $newPath,
            'path_location_temp' => $request->input('save_storage_folder') . "/" . $user_id,
            'typeFile' => $extension,
            'path' => "/media-file/$newPath",
            'path2' => url("api/media-file/$newPath")
        ];
        if (Storage::disk('local')->delete($path)) {
            return [
                'success' => false,
                'message' => "Tipo de archivo no valido"
            ];
        } else {
            return [
                'success' => false,
                'message' => "Error al eliminar el tipo de archivo no valido"
            ];
        }
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes .= ' bytes';
        } elseif ($bytes == 1) {
            $bytes .= ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function get_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                return response()->json([
                    'success' => true,
                    'Results' => ImSolicitud::with([
                        'documents' => function ($query) {
                            $query->where('bol_eliminado', false);
                        },
                        'cat_subcausal_impedimento.cat_plantilla',
                        'impedimento',
                        'impediment',
                        'causales.subcausal.cat_plantilla'
                    ])->find(decrypt($request->hash_id))
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));

                // //------- SNAPSHOT BITACORA SOLICITUD ------
                $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);

                // //------- GUARDAR MOVIMIENTO EN BITACORA GENERAL -----
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "Se actualizó la solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud ".optional($ImSolicitud->cat_type)->tipo_solicitud.", con el estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);
                DB::commit();

                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_cats_index(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'Results' => [
                        "cat_oficinas" => ImCatOficina::porTipoActivas(2)->orderBy('cad_oficina', 'asc')->get(),
                        "causal_impedimento" => ImCatCausalImpedimento::orderBy('causal_impedimento', 'asc')->get(),
                        "cat_estatus" => ImCatEstatusSolicitud::orderBy('estatus_solicitud', 'asc')->get(),
                        "cat_tipo_solicitud" => ImCatTipoSolicitud::orderBy('tipo_solicitud', 'asc')->get(),
                        "usuarios" => User::with('usuarioPerfil')->orderBy('username', 'asc')->get()
                    ],
                ], 200);
                DB::commit();
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function change_status(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $ImSolicitud->update([
                    "id_estatus_solicitud" => 20,
                    "observaciones" => null
                ]);

                // //------- SNAPSHOT BITACORA SOLICITUD ------
                $snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud  con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . " cambio de estatus a " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function send_to_validate(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));

                $data_update = [
                    "id_estatus_solicitud" => 40,
                    "id_usuario_reviso" => $this->id_user
                ];

                if( $ImSolicitud->id_usuario_elaboro == null ){
                    $data_update["id_usuario_elaboro"] = $this->id_user;
                }

                $ImSolicitud->update($data_update);

                // //------- SNAPSHOT BITACORA SOLICITUD ------
                $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function create_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);

                $ImSolicitud = ImSolicitud::with([
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    }
                ])->find(decrypt($request->hash_id));

                $backup_anterior_solicitud = $ImSolicitud->toArray();
                $backup_anterior_solicitud["action"] = "nuevo";

                $data_solicitud = (object) [
                    "id_estatus_solicitud" => 50,
                    "id_usuario_autorizo" => $this->id_user,
                    "fecha_autorizacion" => Carbon::now(),
                    "observaciones" => null,
                    "backup_anterior" => json_encode($backup_anterior_solicitud)
                ];

                if (
                    $ImSolicitud->id_tipo_solicitud == 1 &&
                    optional($ImSolicitud->cat_causal_impedimento)->validate_high
                ) {
                    $data_solicitud->id_estatus_solicitud = 100;
                    $data_solicitud->id_usuario_altas = $this->id_user;
                }

                $ImSolicitud->update(collect($data_solicitud)->toArray());

                $data = collect($ImSolicitud)->toArray();
                $ImPersona = ImPersona::create($data);

                $data["id_persona"] = $ImPersona->id_persona;
                $data["id_estatus_impedimento"] = $data["id_estatus_solicitud"];

                foreach ($data as $key => $value) {
                    if( $key == "id_impedimento" ){
                        unset($data[$key]);
                    }
                }

                $ImImpedimento = ImImpedimento::create($data);

                $ImImpedimento->update(["numero_impedimento" => $ImImpedimento->id_impedimento]);
                $ImPersonaPadre = ImPersonaPadre::create($data);

                $ImSolicitud->update(["id_impedimento" => $ImImpedimento->id_impedimento]);

                foreach ($ImSolicitud->documents as $item) {
                    $item = collect($item)->toArray();
                    $item["id_impedimento"] = $ImImpedimento->id_impedimento;
                    $item["id_usuario_alta"] = $this->id_user;
                    $item["id_usuario_modificacion"] = null;
                    ImImpedimentoDocumento::create($item);
                }

                DB::insert('INSERT INTO im_impedimentos_solicitudes (id_solicitud, id_impedimento) VALUES (?, ?)', [$ImSolicitud->id_solicitud, $ImImpedimento->id_impedimento]);

                $impedimento = ImImpedimento::with([
                    'people.entidadFederativa',
                    'people.paisNacimiento',
                    'people.municipioNacimiento',
                    'fathers',
                    'documents' => function ($query) {
                        $query->with('cat_anexo')->where('bol_eliminado', false);
                    },
                    'cat_office',
                    'usuarioElaboro',
                    'usuarioReviso',
                    'usuarioAutorizo',
                    'usuarioAltas',
                    'cat_causal',
                    'cat_subcausal_impedimento.cat_plantilla'
                ])->find($ImImpedimento->id_impedimento);

                //TODO EJEMPLO DE GUARDAR ESTATUS DE RECHAZO
                $backup_anterior_impedimento = $impedimento->toArray();
                $backup_anterior_impedimento["action"] = "nuevo";
                $impedimento->update(["backup_anterior" => json_encode($backup_anterior_impedimento)]);

                if (
                    $ImSolicitud->id_tipo_solicitud == 1 &&
                    optional($ImSolicitud->cat_causal_impedimento)->validate_high &&
                    $ImSolicitud->correo_electronico
                ) {
                    Mail::to(strtolower($ImSolicitud->correo_electronico))->send(new MailImpediments($impedimento, $ImSolicitud->id_tipo_solicitud));
                }

                $ImSolicitudBitacora = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class);

                // //TODO REVISAR ESTA PARTE CON JOSE LUIS
                // $data_new = array_merge($data, $ImPersonaPadre->only(['nombres_padre', 'primer_apellido_padre', 'segundo_apellido_padre', 'nombres_madre', 'primer_apellido_madre', 'segundo_apellido_madre']));
                // ImPersonaBitacora::create($data_new);
                //$snapshot = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );

                // $usuarioActual = Auth::user();
                // $usuarioActual = Auth::user();
                // $nombreElaboro = optional($ImImpedimento->usuarioElaboro)->username;
                // $usuarioReviso = optional($ImImpedimento->usuarioReviso)->username;
                // $usuarioAutorizo = optional($ImImpedimento->usuarioAutorizo)->username;
                // $usuarioAlta = optional($ImImpedimento->usuarioAltas)->username;
                //$ImImpedimentoBitacora = $this->guardarSnapshot($ImImpedimento, ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $ImSolicitudBitacora->tipo_solicitud]);
                self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó, se cambio el estatus a" . optional($ImSolicitud->cat_status)->estatus_solicitud. ", se creo un impedimento con el ID ".$ImImpedimento->id_impedimento.
                    " con el estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud. " y se creo una boleta de alta"
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'ImImpedimento' => ImImpedimento::with('people')->find($ImImpedimento->id_impedimento)
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function impediment_update(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                $data = $request->all();
                //dd($data);

                if (count($request->documents) > 0) {
                    foreach (collect($request->documents)->where("aux", true) as $item) {
                        $newLocationPath = str_replace(['filesTemp'], 'files', $item["url_documento"]);

                        if (isset($item["id_impedimento_documento"])) {
                            $ImImpedimentoDocumento = ImImpedimentoDocumento::find($item["id_impedimento_documento"]);
                            $ImImpedimentoDocumento->update([
                                "identificador_documento" => $item["identificador_documento"],
                                "url_documento" => $newLocationPath,
                                "id_usuario_modificacion" => $this->id_user,
                                "observaciones" => $item["observaciones"]
                            ]);
                        }

                        $this->moveFileLocation($newLocationPath, $item["url_documento"]);
                    }
                }

                $ImImpedimento = ImImpedimento::with([
                    'people.people_fathers',
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                    'low' => function ($q) {
                        $q->where('bol_eliminado', false);
                    }
                ])->find(decrypt($request->hash_id));

                $data_update = [
                    "id_oficina" => $request->id_oficina,
                    "dependencia" => $request->dependencia,
                    "nombre_dependencia" => $request->nombre_dependencia,
                    "correo_electronico" => $request->correo_electronico,
                    "motivacion_acto_juridico" => $request->motivacion_acto_juridico,
                    "id_causal_impedimento" => $request->id_causal_impedimento,
                    "causal_otro_descripcion" => $request->causal_otro_descripcion,
                    "id_subcausal_impedimento" => $request->id_subcausal_impedimento,
                    "id_usuario_modificacion" => $this->id_user
                ];

                $request->estatus_impedimento = ($request->estatus_impedimento == "Activo" ? 100 : 150);
                $data_update["id_estatus_impedimento"] = $request->estatus_impedimento;
                if( $request->estatus_impedimento != $ImImpedimento->id_estatus_impedimento){
                    $data_update["id_estatus_impedimento"] = $request->estatus_impedimento;
                    $data_update["id_usuario_altas"] = $this->id_user;
                    $data_update["fecha_autorizacion"] = Carbon::now();
                    if( $request->estatus_impedimento == 100 ){
                        if ( $ImImpedimento->low ) {
                            $ImImpedimento->low->id_usuario_modificacion = $this->id_user;
                            $ImImpedimento->low->id_usuario_alta = $this->id_user;
                            if ( $request->estatus_impedimento == 100 ) {
                                $ImImpedimento->low->bol_eliminado = true;
                                $ImImpedimento->low->id_estatus_impedimento_baja = 100;
                                $ImImpedimento->low->id_usuario_modificacion = $this->id_user;
                            }
                            $ImImpedimento->low->save();
                        }
                    }else{
                        if ( $ImImpedimento->low ) {
                            $ImImpedimento->low->id_usuario_modificacion = $this->id_user;
                            $ImImpedimento->low->id_usuario_alta = $this->id_user;
                            $ImImpedimento->low->id_usuario_modificacion = $this->id_user;
                            $ImImpedimento->low->bol_eliminado = false;
                            $ImImpedimento->low->id_estatus_impedimento_baja = 150;
                            $ImImpedimento->low->save();
                        }else{
                            $ImImpedimentoBaja = ImImpedimentoBaja::create([
                                "id_usuario_alta" => $this->id_user,
                                "id_usuario_modificacion" => $this->id_user,
                                "id_oficina" => $request->id_oficina,
                                "id_impedimento" => $ImImpedimento->id_impedimento,
                                "fecha_elaboracion" => Carbon::now(),
                                "id_estatus_impedimento_baja" => 150
                            ]);
                        }
                    }
                }

                $ImImpedimento->update($data_update);
                $ImImpedimento->people()->update([
                    "nombres" => $request->nombres,
                    "primer_apellido" => $request->primer_apellido,
                    "segundo_apellido" => $request->segundo_apellido,
                    "fecha_nacimiento" => Carbon::parse($request->fecha_nacimiento)->format("Y-m-d"),
                    "entidad_federativa_nacimiento" => $request->entidad_federativa_nacimiento,
                    "id_usuario_modificacion" => $this->id_user
                ]);

                $ImImpedimento->people->people_fathers()->update([
                    "nombres_padre" => $request->padre_nombres,
                    "primer_apellido_padre" => $request->padre_primer_apellido,
                    "segundo_apellido_padre" => $request->padre_segundo_apellido,
                    "nombres_madre" => $request->madre_nombres,
                    "primer_apellido_madre" => $request->madre_primer_apellido,
                    "segundo_apellido_madre" => $request->madre_segundo_apellido,
                    "id_usuario_modificacion" => $this->id_user
                ]);

                $impedimentoActualizado = ImImpedimento::with([
                    'people.people_fathers',
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                    'cat_office',
                    'cat_causal',
                    'cat_subcausal',
                    'usuarioElaboro',
                    'usuarioReviso',
                    'usuarioAutorizo',
                    'cat_status',
                    'usuarioAltas'
                ])->find($ImImpedimento->id_impedimento);

                self::save_binnacle_impediment($ImImpedimento->id_impedimento);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => 13,
                    "action"=> "Se actualizó un impedimento con el ID ".$ImImpedimento->id_impedimento.
                    " y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                ]);

                DB::commit();

                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function update_request_impediments(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);
                $ImSolicitud = ImSolicitud::with(['impedimento'])->find(decrypt($request->hash_id));
                $aux = true;
                $snapshot = $this->guardarSnapshot($ImSolicitud, ImSolicitudBitacora::class);
                if ($ImSolicitud->id_estatus_solicitud != 300 && $ImSolicitud->impedimento()->exists()) {
                    $request->id_impedimento = $ImSolicitud->impedimento->id_impedimento;
                    $this->update_impediment($request);
                    $aux = false;
                    $ImImpedimento = ImImpedimento::find($ImSolicitud->impedimento->id_impedimento);
                    //$snapshot = $this->guardarSnapshot($ImImpedimento, \App\Models\ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $snapshot->tipo_solicitud]);
                    self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 2,
                        "cat_module_id" => $request->moduleId,
                        "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                        ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImImpedimento->id_impedimento.
                        " se actualizó y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                    ]);
                }

                if( $aux == true){
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 2,
                        "cat_module_id" => $request->moduleId,
                        "action"=> "La solicitud el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion:" . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    //TODO 317 SE ELIMINO UN IMPEDIMENTO Y NO HAY ESTATUS POR DEFINIR
    public function delete_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $id = $request->id_impedimento;
                if (filter_var($request->is_hash, FILTER_VALIDATE_BOOLEAN)) {
                    $id = decrypt($id);
                }

                $ImImpedimento = ImImpedimento::findOrFail($id);
                $ImImpedimento->bol_eliminado = true;
                //$ImImpedimento->id_estatus_impedimento = 2000;
                $ImImpedimento->save();

                self::save_binnacle_impediment($ImImpedimento->id_impedimento);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 4,
                    "cat_module_id" => 13,
                    "action"=> "Se elimino el impedimento con el ID ".$ImImpedimento->id_impedimento
                ]);
                DB::commit();

                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion:" . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function create_impediment_modify(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $ImSolicitud = ImSolicitud::with([
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                ])->find(decrypt($request->hash_id));

                unset($ImSolicitud->id_impedimento);

                $ImSolicitud->curp = $ImSolicitud->curp_identidad;
                $ImSolicitud->nombres = $ImSolicitud->nombres_identidad;
                $ImSolicitud->primer_apellido = $ImSolicitud->primer_apellido_identidad;
                $ImSolicitud->segundo_apellido = $ImSolicitud->segundo_apellido_identidad;
                $data = collect($ImSolicitud)->toArray();

                $ImPersona = ImPersona::create($data);
                $data["id_persona"] = $ImPersona->id_persona;
                $data["id_estatus_impedimento"] = 100;
                $data["id_subcausal_impedimento"] = $ImSolicitud->id_subcausal_impedimento;
                $data["dependencia"] = $ImSolicitud->dependencia;
                $data['id_tipo_solicitud'] = $ImSolicitud->id_tipo_solicitud;

                $ImImpedimento = ImImpedimento::create($data);
                //dd($ImImpedimento);
                $ImImpedimento->update([
                    "numero_impedimento" => $ImImpedimento->id_impedimento,
                    "id_usuario_autorizo" => $this->id_user,
                    "fecha_autorizacion" => Carbon::now(),
                    "id_usuario_altas" => $this->id_user
                ]);

                DB::insert('INSERT INTO im_impedimentos_solicitudes (id_solicitud, id_impedimento) VALUES (?, ?)', [$ImSolicitud->id_solicitud, $ImImpedimento->id_impedimento]);

                $ImPersonaPadre = ImPersonaPadre::create($data);
                $ImSolicitud->update([
                    "id_estatus_solicitud" => 100,
                    "id_usuario_autorizo" => $this->id_user,
                    "fecha_autorizacion" => Carbon::now(),
                    "id_usuario_altas" => $this->id_user,
                    "id_impedimento" => $ImImpedimento->id_impedimento
                ]);

                foreach ($ImSolicitud->documents as $item) {
                    $item = collect($item)->toArray();
                    $item["id_impedimento"] = $ImImpedimento->id_impedimento;
                    $item["id_usuario_alta"] = $this->id_user;
                    $item["id_usuario_modificacion"] = null;
                    ImImpedimentoDocumento::create($item);
                }

                Mail::to(strtolower($ImSolicitud->correo_electronico))->send(new MailImpediments(
                    ImImpedimento::with([
                        'people.entidadFederativa',
                        'people.paisNacimiento',
                        'people.municipioNacimiento',
                        'fathers',
                        'documents' => function ($query) {
                            $query->with('cat_anexo')->where('bol_eliminado', false);
                        },
                        'cat_office',
                        'usuarioElaboro',
                        'usuarioReviso',
                        'usuarioAutorizo',
                        'usuarioAltas',
                        'cat_causal',
                        'cat_subcausal_impedimento.cat_plantilla'
                    ])->find($ImImpedimento->id_impedimento),
                    $ImSolicitud->id_tipo_solicitud
                ));

                $snapshotSolicitud = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );
                self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ",Se creo un impedimento con el ID ".$ImImpedimento->id_impedimento.
                    " con estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'ImImpedimento' => ImImpedimento::with('people')->find($ImImpedimento->id_impedimento)
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion:" . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function search_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                $ImSolicitud = ImSolicitud::with([
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                ])->find(decrypt($request->hash_id));

                $aux = null;

                //FECHA DE NACIMIENTO TIENE QUE SER WHERE EXACTA

                $ImImpedimento1 = ImImpedimento::with([
                    'low',
                    'cat_causal_impedimento',
                    'cat_subcausal_impedimento.cat_plantilla',
                    'people.people_fathers',
                    'people.genre',
                    'documents' => function ($query) {
                        $query->with(['cat_anexo'])->where('bol_eliminado', false);
                    }
                ])
                    ->whereHas('people', function ($q) use ($ImSolicitud) {
                        return $q->SearchService($ImSolicitud);
                    })
                    ->when($ImSolicitud->id_tipo_solicitud != 3, function ($query) use ($ImSolicitud) {
                        return $query->where('id_causal_impedimento', $ImSolicitud->id_causal_impedimento);
                    })
                    ->where('id_estatus_impedimento', 100)
                    ->where('bol_eliminado', false)
                    ->whereDoesntHave('low')
                    ->get();

                $ImImpedimento2 = ImImpedimento::with([
                    'low',
                    'cat_causal_impedimento',
                    'cat_subcausal_impedimento.cat_plantilla',
                    'people.people_fathers',
                    'people.genre',
                    'documents' => function ($query) {
                        $query->with(['cat_anexo'])->where('bol_eliminado', false);
                    }
                ])
                    ->whereHas('people', function ($q) use ($ImSolicitud) {
                        return $q->SearchService($ImSolicitud);
                    })
                    ->when($ImSolicitud->id_tipo_solicitud != 3, function ($query) use ($ImSolicitud) {
                        return $query->where('id_causal_impedimento', $ImSolicitud->id_causal_impedimento);
                    })
                    ->where('id_estatus_impedimento', 100)
                    ->where('bol_eliminado', false)
                    ->whereHas('low', function ($q) {
                        return $q->where('id_estatus_impedimento_baja', 100)
                            ->where('bol_eliminado', true);
                    })
                    ->get();

                $resultado = $ImImpedimento1->concat($ImImpedimento2);

                $aux = (count($resultado) > 0 ? 1 : 2);

                $page = request()->get('page', 1); // página actual (por defecto 1)
                $perPage = $request->rowsPerPage ?? 10; // número de registros por página

                // Convierte Collection en paginador
                $paginado = new LengthAwarePaginator(
                    $resultado->forPage($page, $perPage),
                    $resultado->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'aux' => $aux,
                    'ImImpedimentos' => $paginado,
                    'aux_dependencia' => $ImSolicitud->dependencia
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }
    public function search_impediment_low(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));

                $ImImpedimento1 = ImImpedimento::with([
                    'low',
                    'cat_causal_impedimento',
                    'cat_subcausal_impedimento.cat_plantilla',
                    'people.people_fathers',
                    'people.genre',
                    'documents' => function ($query) {
                        $query->with(['cat_anexo'])->where('bol_eliminado', false);
                    }
                ])
                    ->whereHas('people', function ($q) use ($ImSolicitud) {
                        return $q->SearchService($ImSolicitud);
                    })
                    ->where('id_estatus_impedimento', 100)
                    ->where('bol_eliminado', false)
                    ->whereDoesntHave('low')
                    ->get();

                $ImImpedimento2 = ImImpedimento::with([
                    'low',
                    'cat_causal_impedimento',
                    'cat_subcausal_impedimento.cat_plantilla',
                    'people.people_fathers',
                    'people.genre',
                    'documents' => function ($query) {
                        $query->with(['cat_anexo'])->where('bol_eliminado', false);
                    }
                ])
                    ->whereHas('people', function ($q) use ($request) {
                        return $q->SearchService($request);
                    })
                    ->where('id_estatus_impedimento', 100)
                    ->where('bol_eliminado', false)
                    ->whereHas('low', function ($q) {
                        return $q->where('id_estatus_impedimento_baja', 100)
                            ->where('bol_eliminado', true);
                    })
                    ->get();

                $resultado = $ImImpedimento1->concat($ImImpedimento2);

                $page = request()->get('page', 1); // página actual (por defecto 1)
                $perPage = $request->rowsPerPage ?? 10; // número de registros por página

                // Convierte Collection en paginador
                $paginado = new LengthAwarePaginator(
                    $resultado->forPage($page, $perPage),
                    $resultado->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url(), 'query' => request()->query()]
                );


                return response()->json([
                    'success' => true,
                    'ImImpedimentos' => $paginado
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function select_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();

                $ImImpedimento = ImImpedimento::with([
                    'people',
                    'fathers',
                    'documents' => function ($query) {
                        $query->with('cat_anexo')->where('bol_eliminado', false);
                    },
                    'low' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                ])->find($request->id_impedimento);

                $backup_impediment = collect($ImImpedimento)->toArray();
                $backup_impediment["action"] = "existe_impedimento";
                $ImImpedimento->backup_anterior = json_encode($backup_impediment);

                $ImSolicitud = ImSolicitud::with([
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                    'cat_causal_impedimento'
                ])->find(decrypt($request->hash_id));

                $backup_solicitud = collect($ImSolicitud)->toArray();
                $backup_solicitud["action"] = "nuevo";
                $ImSolicitud->backup_anterior = json_encode($backup_solicitud);

                if (
                    $ImSolicitud->id_tipo_solicitud == 1 &&
                    optional($ImSolicitud->cat_causal_impedimento)->validate_high
                ) {
                    $ImSolicitud->id_estatus_solicitud = 100;
                    $ImSolicitud->id_usuario_altas = $this->id_user;
                    $ImImpedimento->id_estatus_impedimento = 100;
                } else {
                    $ImSolicitud->id_estatus_solicitud = 50;
                    $ImImpedimento->id_estatus_impedimento = 50;
                }

                $ImPersona = ImPersona::find($request->id_persona);
                $ImPersona->nombres = $ImSolicitud->nombres;
                $ImPersona->primer_apellido = $ImSolicitud->primer_apellido;
                $ImPersona->segundo_apellido = $ImSolicitud->segundo_apellido;
                $ImPersona->correo_electronico = $ImSolicitud->persona_correo_electronico;
                $ImPersona->fecha_nacimiento = $ImSolicitud->fecha_nacimiento;
                $ImPersona->curp = ($ImSolicitud->curp ? $ImSolicitud->curp : $ImPersona->curp);
                $ImPersona->entidad_federativa_nacimiento = $ImSolicitud->entidad_federativa_nacimiento;
                $ImPersona->id_pais_nacimiento = $ImSolicitud->id_pais_nacimiento;
                $ImPersona->id_usuario_modificacion = $this->id_user;
                $ImPersona->save();

                $ImPersonaPadre = ImPersonaPadre::where("id_persona", $ImPersona->id_persona)->first();
                $ImPersonaPadre->fill(collect($ImSolicitud->fathers)->toArray());
                $ImPersonaPadre->save();

                $ImImpedimento->motivacion_acto_juridico = $ImImpedimento->motivacion_acto_juridico . "\n" . $ImSolicitud->motivacion_acto_juridico;
                $ImImpedimento->id_usuario_autorizo = $this->id_user;
                $ImImpedimento->fecha_autorizacion = Carbon::now();

                $ImImpedimento->save();
                DB::insert('INSERT INTO im_impedimentos_solicitudes (id_solicitud, id_impedimento) VALUES (?, ?)', [$ImSolicitud->id_solicitud, $ImImpedimento->id_impedimento]);

                foreach ($ImImpedimento->documents as $item) {
                    $item->bol_eliminado = true;
                    $item->save();
                }

                foreach ($ImSolicitud->documents as $item) {
                    $item = collect($item)->toArray();
                    $item["id_impedimento"] = $ImImpedimento->id_impedimento;
                    $item["id_usuario_alta"] = $this->id_user;
                    $item["id_usuario_modificacion"] = $this->id_user;
                    ImImpedimentoDocumento::create($item);
                }

                $ImSolicitud->motivacion_acto_juridico = $ImImpedimento->motivacion_acto_juridico;
                $ImSolicitud->id_impedimento = $ImImpedimento->id_impedimento;
                $ImSolicitud->save();

                if (
                    $ImSolicitud->id_tipo_solicitud == 1 &&
                    optional($ImSolicitud->cat_causal_impedimento)->validate_high &&
                    $ImSolicitud->correo_electronico
                ) {
                    Mail::to(strtolower($ImSolicitud->correo_electronico))->send(new MailImpediments(
                        ImImpedimento::with([
                            'people.entidadFederativa',
                            'people.paisNacimiento',
                            'people.municipioNacimiento',
                            'fathers',
                            'documents' => function ($query) {
                                $query->with('cat_anexo')->where('bol_eliminado', false);
                            },
                            'cat_office',
                            'usuarioElaboro',
                            'usuarioReviso',
                            'usuarioAutorizo',
                            'usuarioAltas',
                            'cat_causal',
                            'cat_subcausal_impedimento.cat_plantilla'
                        ])->find($ImImpedimento->id_impedimento),
                        $ImSolicitud->id_tipo_solicitud
                    ));
                }

                $ImImpedimento->id_tipo_solicitud = $ImSolicitud->id_tipo_solicitud;

                $snapshotSolicitud = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );
                self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImImpedimento->id_impedimento.
                    " se actualizó y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                ]);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'ImImpedimento' => ImImpedimento::with('people')->find($ImImpedimento->id_impedimento)
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function create_impediment_low(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();

                $ImSolicitud = ImSolicitud::with([
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    }
                ])->find(decrypt($request->hash_id));

                $backup_solicitud = collect($ImSolicitud)->toArray();
                $backup_solicitud["action"] = "nuevo";
                $ImSolicitud->update([
                    "backup_anterior" => json_encode($backup_solicitud),
                    "id_impedimento" => $request->id_impedimento
                ]);

                $ImImpedimento = ImImpedimento::with([
                    'people',
                    'fathers',
                    'documents' => function ($query) {
                        $query->with('cat_anexo')->where('bol_eliminado', false);
                    },
                    'low' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                ])->find($request->id_impedimento);

                $backup_impediment = collect($ImImpedimento)->toArray();
                $backup_impediment["action"] = "existe_impedimento";
                $backup_impediment["backup_anterior"] = null;
                $backup_impediment = json_encode($backup_impediment);

                DB::insert('INSERT INTO im_impedimentos_solicitudes (id_solicitud, id_impedimento) VALUES (?, ?)', [$ImSolicitud->id_solicitud, $request->id_impedimento]);
                $ImSolicitud->id_estatus_solicitud = 50;
                $ImSolicitud->id_usuario_autorizo = $this->id_user;
                $ImSolicitud->fecha_autorizacion = Carbon::now();
                $ImSolicitud->save();

                $ImPersona = ImPersona::find($request->id_persona);
                $ImPersona->nombres = $ImSolicitud->nombres;
                $ImPersona->primer_apellido = $ImSolicitud->primer_apellido;
                $ImPersona->segundo_apellido = $ImSolicitud->segundo_apellido;
                $ImPersona->correo_electronico = $ImSolicitud->persona_correo_electronico;
                $ImPersona->fecha_nacimiento = $ImSolicitud->fecha_nacimiento;
                $ImPersona->curp = ($ImSolicitud->curp ? $ImSolicitud->curp : $ImPersona->curp);
                $ImPersona->entidad_federativa_nacimiento = $ImSolicitud->entidad_federativa_nacimiento;
                $ImPersona->id_pais_nacimiento = $ImSolicitud->id_pais_nacimiento;
                $ImPersona->id_usuario_modificacion = $this->id_user;
                $ImPersona->save();

                $ImPersonaPadre = ImPersonaPadre::where("id_persona", $ImPersona->id_persona)->first();
                $ImPersonaPadre->nombres_padre = $ImSolicitud->nombres_padre;
                $ImPersonaPadre->primer_apellido_padre = $ImSolicitud->primer_apellido_padre;
                $ImPersonaPadre->segundo_apellido_padre = $ImSolicitud->segundo_apellido_padre;
                $ImPersonaPadre->nombres_madre = $ImSolicitud->nombres_madre;
                $ImPersonaPadre->primer_apellido_madre = $ImSolicitud->primer_apellido_madre;
                $ImPersonaPadre->segundo_apellido_madre = $ImSolicitud->segundo_apellido_madre;
                $ImPersonaPadre->save();

                $request->impediment = (object) $request->impediment;
                $ImImpedimento = ImImpedimento::with([
                    'documents' => function ($query) {
                        $query->where('bol_eliminado', false);
                    },
                ])->find($request->id_impedimento);

                $ImSolicitud->id_estatus_impedimento = 50;
                $ImImpedimento->update(collect($ImSolicitud)->toArray());
                $ImImpedimentoBaja = ImImpedimentoBaja::create([
                    "id_usuario_alta" => $this->id_user,
                    "id_usuario_modificacion" => $this->id_user,
                    "id_oficina" => $ImSolicitud->id_oficina,
                    "id_impedimento" => $ImImpedimento->id_impedimento,
                    "fecha_elaboracion" => Carbon::now(),
                    "id_estatus_impedimento_baja" => 50
                ]);

                $ImImpedimento->update([
                    "id_usuario_autorizo" => $this->id_user,
                    "fecha_autorizacion" => Carbon::now(),
                    "backup_anterior" => $backup_impediment
                ]);

                $ImImpedimento = ImImpedimento::with('people')->find($ImImpedimento->id_impedimento);
                $snapshotSolicitud = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );
                self::save_binnacle_impediment($ImImpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImImpedimento->id_impedimento.
                    " se actualizó y cambio de estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'ImImpedimento' => $ImImpedimento
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }


    public function send_to_authorize(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $data = collect(value: $ImSolicitud)->toArray();
                $ImPersona = ImPersona::create($data);

                $data["id_persona"] = $ImPersona->id_persona;
                $data["id_estatus_impedimento"] = 50;
                $ImImpedimento = ImImpedimento::create($data);
                $ImImpedimento->update([
                    "numero_impedimento" => $ImImpedimento->id_impedimento
                ]);
                $ImPersonaPadre = ImPersonaPadre::create($data);
                $ImSolicitud->update(["id_estatus_solicitud" => 50]);

                $ImImpedimento->id_tipo_solicitud = $ImSolicitud->id_tipo_solicitud;
                $solicitudSnapshot = $this->guardarSnapshot($ImSolicitud, ImSolicitudBitacora::class);

                ImPersonaBitacora::create(array_merge($data, $ImPersonaPadre->only(['nombres_padre', 'primer_apellido_padre', 'segundo_apellido_padre', 'nombres_madre', 'primer_apellido_madre', 'segundo_apellido_madre'])));

                $this->guardarSnapshot($ImImpedimento, ImImpedimentoBitacora::class, [], ['tipo_solicitud', $solicitudSnapshot->tipo_solicitud]);

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    //TODO AUTORIZO ALTAS
    public function send_to_active(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);
                $ImSolicitud = ImSolicitud::with(['impedimento'])->find(decrypt($request->hash_id));

                if ( $ImSolicitud->impedimento ) {
                    $request->id_impedimento = $ImSolicitud->impedimento->id_impedimento;
                    $this->update_impediment($request);
                    $ImSolicitud->impedimento->id_estatus_impedimento = 100;
                    $ImSolicitud->impedimento->id_usuario_altas = $this->id_user;
                    $ImSolicitud->impedimento->save();

                    Mail::to(strtolower($ImSolicitud->correo_electronico))->send(new MailImpedimentsHigh(
                        ImImpedimento::with([
                            'people.entidadFederativa',
                            'people.paisNacimiento',
                            'people.municipioNacimiento',
                            'fathers',
                            'documents' => function ($query) {
                                $query->with('cat_anexo')->where('bol_eliminado', false);
                            },
                            'cat_office',
                            'usuarioElaboro',
                            'usuarioReviso',
                            'usuarioAutorizo',
                            'usuarioAltas',
                            'cat_causal',
                            'cat_subcausal_impedimento.cat_plantilla'
                        ])->find($ImSolicitud->impedimento->id_impedimento),
                        $ImSolicitud
                    ));
                }

                $ImSolicitud->update([
                    "id_estatus_solicitud" => 100,
                    "id_usuario_altas" => $this->id_user
                ]);
                $Imimpedimento = ImImpedimento::find($request->id_impedimento);
                $Imimpedimento->id_estatus_impedimento = 100;

                $Imimpedimento = ImImpedimento::find($request->id_impedimento);

                if ($Imimpedimento && $Imimpedimento->fathers()->exists()) {

                    $Imimpedimento->fathers()->update([
                        'nombres_padre' => $ImSolicitud->nombres_padre,
                        'primer_apellido_padre' => $ImSolicitud->primer_apellido_padre,
                        'segundo_apellido_padre' => $ImSolicitud->segundo_apellido_padre,
                        'nombres_madre' => $ImSolicitud->nombres_madre,
                        'primer_apellido_madre' => $ImSolicitud->primer_apellido_madre,
                        'segundo_apellido_madre' => $ImSolicitud->segundo_apellido_madre,
                    ]);
                }
                $solicitudSnapshot = $this->guardarSnapshot($ImSolicitud, ImSolicitudBitacora::class);

                //$this->guardarSnapshot($Imimpedimento, ImImpedimentoBitacora::class, [], ['tipo_solicitud' => $solicitudSnapshot->tipo_solicitud]);
                self::save_binnacle_impediment($Imimpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$Imimpedimento->id_impedimento.
                    " se actualizó y cambio de estatus ".optional($Imimpedimento->cat_status)->estatus_solicitud
                ]);

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function send_to_active_low(Request $request)
    {
        DB::beginTransaction();

        try {
            if ($request->wantsJson()) {
                $this->update_request($request);
                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $ImSolicitud = ImSolicitud::with([
                    "impedimento" => function ($query) {
                        $query->with([
                            'low' => function ($q) {
                                $q->where('id_estatus_impedimento_baja', 250)
                                    ->where('bol_eliminado', false);
                            }
                        ]);
                    },
                ])
                ->where("id_estatus_solicitud", 250)
                ->where("id_tipo_solicitud", 2)
                ->find(decrypt($request->hash_id));

                $request->id_impedimento = $ImSolicitud->impedimento->id_impedimento;
                $this->update_impediment($request);

                if ( $ImSolicitud->impedimento ) {
                    $ImSolicitud->impedimento->id_estatus_impedimento = 150;
                    $ImSolicitud->impedimento->save();
                    $ImSolicitud->impedimento->low->id_estatus_impedimento_baja = 150;
                    $ImSolicitud->impedimento->low->save();

                    $Imimpedimento = ImImpedimento::find($request->id_impedimento);
                    $Imimpedimento->id_estatus_impedimento = 150;

                    if ($ImSolicitud->correo_electronico) {
                        Mail::to(strtolower($ImSolicitud->correo_electronico))->send(new MailImpedimentsLow(
                            ImImpedimento::with([
                                'people.entidadFederativa',
                                'people.paisNacimiento',
                                'people.municipioNacimiento',
                                'fathers',
                                'documents' => function ($query) {
                                    $query->with('cat_anexo')->where('bol_eliminado', false);
                                },
                                'cat_office',
                                'usuarioElaboro',
                                'usuarioReviso',
                                'usuarioAutorizo',
                                'usuarioAltas',
                                'cat_causal',
                                'cat_subcausal_impedimento.cat_plantilla'
                            ])->find($ImSolicitud->impedimento->id_impedimento),
                            $ImSolicitud
                        ));
                    }
                }

                $ImSolicitud->update([
                    "id_estatus_solicitud" => 150
                ]);

                $snapshotSolicitud = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );
                self::save_binnacle_impediment($Imimpedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$Imimpedimento->id_impedimento.
                    " se actualizó y cambio de estatus ".optional($Imimpedimento->cat_status)->estatus_solicitud
                ]);
                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }

            // 1️⃣ Actualizar solicitud primero
            $this->update_request($request);

            // 2️⃣ Volver a cargarla fresca
            $ImSolicitud = ImSolicitud::with([
                'impediment.low'
            ])->findOrFail(decrypt($request->hash_id));

            $impedimento = $ImSolicitud->impediment->first();

            if (!$impedimento) {
                throw new \Exception("No se encontró impedimento.");
            }

            $request->id_impedimento = $impedimento->id_impedimento;

            // 3️⃣ Actualizar impedimento
            $this->update_impediment($request);

            // 4️⃣ Cambiar estatus
            $impedimento->update([
                'id_estatus_impedimento' => 150
            ]);

            if ($impedimento->low) {
                $impedimento->low->update([
                    'id_estatus_impedimento_baja' => 150
                ]);
            }

            $ImSolicitud->update([
                'id_estatus_solicitud' => 150
            ]);

            $padres = $impedimento->fathers;

            if ($padres) {
                $padres->update([
                    'nombres_padre' => $ImSolicitud->nombres_padre,
                    'primer_apellido_padre' => $ImSolicitud->primer_apellido_padre,
                    'segundo_apellido_padre' => $ImSolicitud->segundo_apellido_padre,
                    'nombres_madre' => $ImSolicitud->nombres_madre,
                    'primer_apellido_madre' => $ImSolicitud->primer_apellido_madre,
                    'segundo_apellido_madre' => $ImSolicitud->segundo_apellido_madre,
                ]);
            }
            // 6️⃣ Snapshot
            $solicitudSnapshot = $this->guardarSnapshot(
                $ImSolicitud,
                \App\Models\ImSolicitudBitacora::class,
            );

            $this->guardarSnapshot(
                $impedimento,
                ImImpedimentoBitacora::class,
                [],
                ['tipo_solicitud' => $solicitudSnapshot->tipo_solicitud]
            );

            $this->guardarMovimiento(
                $this->id_user,
                $request->moduleId,
                2,
                'Se activó la solicitud con el ID ' . $ImSolicitud->id_solicitud
            );

            DB::commit();

            return response()->json([
                'success' => true
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }


    public function send_to_confirm_low(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $this->update_request($request);

                $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
                $ImSolicitud = ImSolicitud::with([
                    "impedimento" => function ($query) {
                        $query->with([
                            'low' => function ($q) {
                                $q->where('id_estatus_impedimento_baja', 50)
                                    ->where('bol_eliminado', false);
                            }
                        ])
                        ->whereHas('low', function ($q) {
                            return $q->where('id_estatus_impedimento_baja', 50)
                                ->where('bol_eliminado', false);
                        });
                    },
                ])
                ->where("id_estatus_solicitud", 50)
                ->where("id_tipo_solicitud", 2)
                ->whereHas('impedimento', function ($q) {
                    return $q->whereHas('low', function ($q) {
                            return $q->where('id_estatus_impedimento_baja', 50)
                                ->where('bol_eliminado', false);
                        })
                        ->where('bol_eliminado', false);
                })
                ->find(decrypt($request->hash_id));

                $request->id_impedimento = $ImSolicitud->impedimento->id_impedimento;
                $this->update_impediment($request);

                //dd($this->id_user,$ImSolicitud->impedimento);
                if ($ImSolicitud->impedimento ) {
                    $ImSolicitud->impedimento->id_estatus_impedimento = 250;
                    $ImSolicitud->impedimento->save();
                    $ImSolicitud->impedimento->low->id_estatus_impedimento_baja = 250;
                    $ImSolicitud->impedimento->low->id_usuario_alta = $this->id_user;
                    $ImSolicitud->impedimento->low->id_usuario_modificacion = $this->id_user;
                    $ImSolicitud->impedimento->low->save();
                }

                $ImSolicitud->update([
                    "id_estatus_solicitud" => 250
                ]);

                $snapshotSolicitud = $this->guardarSnapshot($ImSolicitud, \App\Models\ImSolicitudBitacora::class, );
                self::save_binnacle_impediment($ImSolicitud->impedimento->id_impedimento,$ImSolicitud->id_tipo_solicitud);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", se actualizó y cambio de estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud. ", el impedimento con el ID ".$ImSolicitud->impedimento->id_impedimento.
                    " se actualizó y cambio de estatus ".optional($ImSolicitud->impedimento->cat_status)->estatus_solicitud
                ]);

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function searchCurpUser(Request $request)
    {
        $user = 'wsgestion';
        $password = 'wsgestion2011';
        $tipoTransaccion = 5;
        //$curp            = 'REMJ910116HVZYRL01';
        $curp = $request->curp;
        //TIRL920313HGTRDS02
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://172.18.203.9/WebServicesGestion/services/ConsultaPorCurpService?wsdl",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"\r\n    xmlns:se=\"http://services.wserv.ecurp.dgti.segob.gob.mx\"\r\n
                xmlns:xsd=\"http://services.wserv.ecurp.dgti.segob.gob.mx/xsd\">\r\n
                <SOAP-ENV:Header/>\r\n
                    <SOAP-ENV:Body>\r\n
                        <se:consultarPorCurp>\r\n
                        <se:datos>\r\n
                                <xsd:cveCurp>" . $curp . "</xsd:cveCurp>\r\n
                                <xsd:cveEntidadEmisora>null</xsd:cveEntidadEmisora>\r\n
                                <xsd:direccionIp>null</xsd:direccionIp>\r\n
                                <xsd:password>" . $password . "</xsd:password>\r\n
                                <xsd:tipoTransaccion>" . $tipoTransaccion . "</xsd:tipoTransaccion>\r\n
                                <xsd:usuario>" . $user . "</xsd:usuario>\r\n
                            </se:datos>\r\n
                        </se:consultarPorCurp>\r\n
                    </SOAP-ENV:Body>\r\n
                </SOAP-ENV:Envelope>",



            CURLOPT_HTTPHEADER => array(

                "Cache-Control: no-cache",
                "Content-Type: text/xml",

            ),
        ));

        $response = curl_exec($curl);  //this response is a xml inside another xml

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            try {
                $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
                $xml = new \SimpleXMLElement($response); //convert the first xml

                $body = $xml->xpath('//nsreturn')[0];

                $xml = new \SimpleXMLElement($body); //convert the second xml

                $array = json_decode(json_encode((array) $xml), TRUE); //conver the xml to array


                if ($array['@attributes']['statusOper'] == "EXITOSO") { ///validate if curp exits

                    foreach ($array as $key => $value) {
                        if (
                            (is_array($value) && empty($value)) ||
                            (is_object($value) && empty((array) $value))
                        ) {
                            $array[$key] = null;
                        }
                    }

                    if( isset( $array['sexo'] ) ){
                        $array['sexo'] = ($array['sexo'] == 'H') ? 1 : 2;
                    }

                    return response()->json([
                        'status' => true,
                        'curp' => $array['CURP'],
                        'apellido_paterno' => $array['apellido1'],
                        'apellido_materno' => $array['apellido2'],
                        'nombres' => $array['nombres'],
                        'id_genero' => $array['sexo'],
                        'fecha_nacimiento' => $array['fechNac'],
                        'nacionalidad' => $array['nacionalidad'],
                        'entidad' => $array['cveEntidadNac'],
                    ]);


                } else {
                    return response()->json([
                        'status' => false,

                    ]);
                }
            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'error' => $e

                ]);
            }

        }

    }
    public function print_impediment(Request $request)
    {
        $id = $request->id_impedimento;
        if (filter_var($request->is_hash, FILTER_VALIDATE_BOOLEAN)) {
            $id = decrypt($id);
        }

        $ImImpedimento = ImImpedimento::with('requests')->findOrFail($id);
        // if (!$request->id_solicitud) {
        //     $request->id_solicitud = $ImImpedimento->requests[0]->id_solicitud;
        // }

       // $ImSolicitud = ImSolicitud::findOrFail($request->id_solicitud);

        $ImImpedimento = ImImpedimento::with(relations: [
            'people.entidadFederativa',
            'people.paisNacimiento',
            'people.municipioNacimiento',
            'fathers',
            'documents' => function ($query) {
                $query->with('cat_anexo')->where('bol_eliminado', false);
            },
            'cat_office',
            'usuarioElaboro',
            'usuarioReviso',
            'usuarioAutorizo',
            'usuarioAltas',
            'low' => function ($q) {
                $q->with('cat_user_alta')->where('bol_eliminado', false);
            }
        ])->findOrFail($id);
        $Image = app(GeneralController::class);

        $documentoAnexo12 = null;

        foreach ($ImImpedimento->documents as $doc) {
            if ($doc->id_cat_anexos == 12) {
                if (!empty($doc->url_documento)) {
                    $path = str_replace('/media-file', '', $doc->url_documento);
                    $doc->url_documento = $Image->getImageFilesWatermarkPDF($path);
                    \Log::info('Base64 generado:', [
                        'base64_start' => substr($doc->url_documento, 0, 200)
                    ]);
                } else {
                    $doc->url_documento = null;
                }

                // Puedes asignar este documento a una variable si lo vas a usar en la vista:
                $documentoAnexo12 = $doc;
                break; // Solo necesitas el primero que coincida
            }
        }

        //dd($ImImpedimento->usuarioElaboro);

        $html = view('pdf.impedimento', compact('ImImpedimento'))->render();

        $pdf = Pdf::loadHTML($html);

        Transaccion::create([
            "user_id" => $this->id_user,
            "cat_transaction_type_id" => 11,
            "cat_module_id" => $request->moduleId,
            "action"=> "Se descargo la boleta de un impedimento con el ID ".$ImImpedimento->id_impedimento.
            " con el estatus ".optional($ImImpedimento->cat_status)->estatus_solicitud
        ]);

        return $pdf->download('impedimento_' . $ImImpedimento->id_impedimento . '.pdf');

    }

    //TODO REVISAR PDF
    public function download_request_report(Request $request){
        DB::beginTransaction();
        try{
            if($request->wantsJson()){
                //dd($request->all());
                $id = $request->id_solicitud;
                if (filter_var($request->is_hash, FILTER_VALIDATE_BOOLEAN)) {
                    $id = decrypt($id);
                }

                $ImSolicitud = ImSolicitud::with(relations: [
                    'cat_office',
                    'cat_type',
                    'cat_causal_impedimento',
                    'cat_subcausal_impedimento',
                    'documents',
                    'cat_user_elaboro',
                    'cat_user_reviso'
                ])->findOrFail($id);

                foreach ($ImSolicitud->documents as $doc) {

                    if (!empty($doc->url_documento)) {
                        $path = str_replace('/media-file', '', $doc->url_documento);
                        $doc->url_documento = $path;

                    } else {
                        $doc->url_documento = null;
                    }

                    // Puedes asignar este documento a una variable si lo vas a usar en la vista:
                    $documentoAnexo12 = $doc;
                    break; // Solo necesitas el primero que coincida

                }

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 11,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "Se descargo el formato de la solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud . " con el estatus " . optional($ImSolicitud->cat_status)->estatus_solicitud
                ]);

                DB::commit();

                $pdf = Pdf::loadView('pdf.report_request', data: compact('ImSolicitud'));
                return $pdf->download('reporte_solicitud.pdf');


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


    public function desassign_requests(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();

                // Desasignación múltiple
                if (!empty($data['selectedItemIds'])) {
                    foreach ($data['selectedItemIds'] as $id) {
                        // Aquí asumimos que los ids múltiples no están cifrados
                        ImAsignacionSolicitudes::where('id_solicitud', $id)->delete();
                    }

                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 13,
                        "cat_module_id" => 23,
                        "action"=> "Se han desasignado las solicitudes con los IDs: " . implode(', ', $data['selectedItemIds'])
                    ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Solicitudes desasignadas correctamente (múltiple).'
                    ], 200);
                }

                // Desasignación individual
                if (!empty($data['selectedItemId'])) {
                    $selectedItemId = decrypt($data['selectedItemId']);
                    ImAsignacionSolicitudes::where('id_solicitud', $selectedItemId)->delete();
                    Transaccion::create([
                        "user_id" => $this->id_user,
                        "cat_transaction_type_id" => 13,
                        "cat_module_id" => 23,
                        "action"=> "Se ha desasignado la solicitud con el IDs: " . $selectedItemId
                    ]);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Solicitud desasignada correctamente (individual).'
                    ], 200);
                }

                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No se enviaron IDs para desasignar.'
                ], 400);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al desasignar solicitudes: ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function assign_requests(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {
                $data = $request->all();
                $selected_requests = $data['selectedItems'];
                $id_usuario = $data['id_usuario'];
                $user = User::find($id_usuario);
                foreach ($selected_requests as $id_solicitud) {
                    ImAsignacionSolicitudes::create([
                        'id_solicitud' => $id_solicitud,
                        'id_usuario' => $id_usuario,
                    ]);
                }

                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 12,
                    "cat_module_id" => 23,
                    "action"=> "El usuario " . $user->username . " se le han asignado las solicitudes con los IDs: " . implode(', ', $selected_requests)
                ]);

                DB::commit();
                return response()->json([
                    'success' => true
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar solicitudes a usuario' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function if_exists_impediment(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->wantsJson()) {

                $ImSolicitud = ImSolicitud::with('impediment')->find(decrypt($request->hash_id));

                return response()->json([
                    'success' => true,
                    'Results' => $ImSolicitud->impediment()->exists(),
                ], 200);
            } else {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Funcion error NombreFuncion: $e->getMessage()");
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getline(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 300);
        }
    }

    public function get_subcausal_plantilla(Request $request)
    {
        $request->validate(rules: [
            'id_subcausal_impedimento' => 'required|integer',
        ]);

        // NO necesitas transacción para un GET
        try {
            $sub = ImCatSubcausalImpedimento::with('cat_plantilla')
                ->find($request->id_subcausal_impedimento);


            // Usa nullsafe operator o data_get según el nombre real de columna en im_plantillas
            // Si la columna se llama 'contenido':
            $contenido = $sub?->cat_plantilla?->plantilla;

            // Si la columna se llama 'plantilla', usa:
            // $contenido = $sub?->cat_plantilla?->plantilla;

            return response()->json([
                'success' => true,
                'Results' => $contenido,  // el front ya lee data.Results
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
    public function get_causal_subcausal_from_impediments_id(Request $request)
    {
        try {
            $data = $request->validate([
                'ids_impedimento' => 'array',
                'ids_impedimento.*' => 'integer'
            ]);

            $ids = $data['ids_impedimento'];

            $ImImpedimento = ImImpedimento::query()->with('cat_causal', 'cat_subcausal.cat_plantilla')->whereIn('id_impedimento', $ids)->get();
            $results = $ImImpedimento->map(function ($imp) {

                return [
                    'id_impedimento' => $imp->id_impedimento,
                    'causal_str' => optional($imp->cat_causal)->causal_impedimento ?? '',
                    'subcausal_str' => optional($imp->cat_subcausal)->subcausal_impedimento ?? '',
                    'plantilla_str' => optional($imp->cat_subcausal)->cat_plantilla->plantilla ?? '',
                ];
            })->values();

            return response()->json([
                'success' => true,
                'Results' => $results,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function send_to_dictaminate(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->wantsJson()) {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }

            $data = $request->validate([
                'hash_id' => 'string',
                'cuerpo_correo' => 'required|string',
                'verificacion_impedimentos' => 'array',
            ]);

            $ImSolicitud = ImSolicitud::where('id_solicitud', decrypt($data['hash_id']))
                ->firstOrFail();
            $ImSolicitud->update([
                'cuerpo_correo' => $data['cuerpo_correo'],
                'id_estatus_solicitud' => 250,
                'id_estatus_verificacion' => 3,
                'verificacion_impedimentos' => count($data['verificacion_impedimentos']) > 0 ? $data['verificacion_impedimentos'] : null
            ]);

            $this->guardarSnapshot($ImSolicitud, ImSolicitudBitacora::class);
            Transaccion::create([
                "user_id" => $this->id_user,
                "cat_transaction_type_id" => 2,
                "cat_module_id" => $request->moduleId,
                "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                ", se actualizó, se cambio el estatus a " . optional($ImSolicitud->cat_status)->estatus_solicitud
            ]);

            DB::commit();

            return response()->json(['success' => true], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function update_cuerpo_correo(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$request->wantsJson()) {
                DB::rollBack();
                return response()->view('errors.404', [], 404);
            }

            $ImSolicitud = ImSolicitud::find(decrypt($request->hash_id));
            $ImSolicitud->update([
                'cuerpo_correo' => $request->cuerpo_correo,
                'id_estatus_verificacion' => $request->id_estatus_verificacion,
            ]);

            Transaccion::create([
                "user_id" => $this->id_user,
                "cat_transaction_type_id" => 2,
                "cat_module_id" => $request->moduleId,
                "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                ", actualizó el cuerpo del correo y cambio el estatus de verificación " . optional($ImSolicitud->cat_status_verificacion)->estatus
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'Results' => [
                    'cuerpo_correo' => $ImSolicitud->cuerpo_correo,
                    'id_estatus_verificacion' => $ImSolicitud->id_estatus_verificacion,
                ]
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al mostrar información ' . $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    //TODO FALTA LA TRANSACCION
    public function send_to_verify(Request $request){
        DB::beginTransaction();
        try{
            if($request->wantsJson()){
                $payload = $request->validate([
                    'hash_id' => 'required',
                    'cuerpo_correo' => 'required|string',
                    'id_estatus_verificacion' => 'required|integer|in:2,3',
                    'id_estatus_solicitud' => 'required|integer'
                ]);

                $idSolicitud = decrypt($payload['hash_id']);
                $ImSolicitud = ImSolicitud::find($idSolicitud);
                $ImSolicitud->update([
                    'cuerpo_correo' => $payload['cuerpo_correo'],
                    'id_estatus_verificacion' => $payload['id_estatus_verificacion'],
                    'id_estatus_solicitud' => $payload['id_estatus_solicitud'],
                    'updated_at' => now(),
                ]);

                if ($ImSolicitud->correo_electronico) {
                    $estatus = (int) $payload['id_estatus_verificacion']; // 2 = con, 3 = sin
                    if (!empty($ImSolicitud->correo_electronico) && in_array($estatus, [2, 3], true)) {
                        Mail::to(strtolower($ImSolicitud->correo_electronico))
                            ->send(new VerificationResultMail(
                                ( !empty($ImSolicitud->verificacion_impedimentos) ),
                                $ImSolicitud->cuerpo_correo
                            ));
                    }
                }

                $this->guardarSnapshot($ImSolicitud, ImSolicitudBitacora::class);
                Transaccion::create([
                    "user_id" => $this->id_user,
                    "cat_transaction_type_id" => 2,
                    "cat_module_id" => $request->moduleId,
                    "action"=> "La solicitud con el ID $ImSolicitud->id_solicitud, tipo de solicitud " . optional($ImSolicitud->cat_type)->tipo_solicitud .
                    ", actualizó el cuerpo del correo, con estatus el ".optional($ImSolicitud->cat_status)->estatus_solicitud." y con el estatus de verificación " . optional($ImSolicitud->cat_status_verificacion)->estatus
                ]);

                DB::commit();
                return response()->json(['success' => true], 200);
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
}
