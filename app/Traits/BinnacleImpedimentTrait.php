<?php

namespace App\Traits;

use App\Models\ImImpedimento;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ImImpedimentoBitacora;
use Carbon\Carbon;

trait BinnacleImpedimentTrait
{

    private $ImImpedimentBitacora = [
        'id_impedimento',
        'id_oficina',
        'correo_electronico',
        'numero_documento',
        'id_estatus_impedimento',
        'cad_oficina',
        'motivacion_acto_juridico',
        'id_causal_impedimento',
        'causal_otro_descripcion',
        'id_subcausal_impedimento',
        'anexo_expediente_integrado',
        'anexo_dictamen_verificacion',
        'numero_pasaporte_cancelado',
        'otro_documento_soporte',
        'observaciones',
        'numero_impedimento',
        'dependencia',
        'nombre_dependencia',
        'fecha_autorizacion',
        'id_usuario_elaboro',
        'id_usuario_reviso',
        'id_usuario_autorizo',
        'id_usuario_altas',
        'bol_eliminado',
        'id_usuario_alta',
        'id_usuario_modificacion',
        'created_at',
        'updated_at'
    ];

    private $ImImpedimentPeopleBitacora = [
        'id_persona',
        'id_persona_consolidada',
        'nombres',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'curp',
        'id_genero',
        'entidad_federativa_nacimiento',
        'id_pais_nacimiento',
        'id_entidad_federativa_nacimiento',
        'id_municipio_nacimiento',
        'created_at',
        'updated_at'
    ];

    private $ImImpedimentPhatersBitacora = [
        'nombres_padre',
        'primer_apellido_padre',
        'segundo_apellido_padre',
        'nombres_madre',
        'primer_apellido_madre',
        'segundo_apellido_madre',
        'created_at',
        'updated_at'
    ];

    protected $insert_impediment = [];

    public function save_binnacle_impediment($id_impedimento, $id_tipo_solicitud = null)
    {

        DB::beginTransaction();

        try {

            if($id_tipo_solicitud){
                $this->insert_impediment['id_tipo_solicitud'] = $id_tipo_solicitud;
            }

            $ImImpedimento = ImImpedimento::with([
                "people.people_fathers",
                'low' => function ($query) {
                    $query->where('bol_eliminado', false);
                },
            ])->find($id_impedimento);


            /*
            |--------------------------------------------------------------------------
            | IMPEDIMENTO
            |--------------------------------------------------------------------------
            */

            foreach ($ImImpedimento->getAttributes() as $key => $value) {

                if (in_array($key, $this->ImImpedimentBitacora)) {

                    if ($key == 'created_at') {
                        $this->insert_impediment['created_at_impedimento'] = $this->formatDate($value);
                        continue;
                    }

                    if ($key == 'updated_at') {
                        $this->insert_impediment['updated_at_impedimento'] = $this->formatDate($value);
                        continue;
                    }

                    $this->insert_impediment[$key] = $value;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PERSONA
            |--------------------------------------------------------------------------
            */

            if ($ImImpedimento->people) {

                foreach ($ImImpedimento->people->getAttributes() as $key_people => $value_people) {

                    if (in_array($key_people, $this->ImImpedimentPeopleBitacora)) {

                        if ($key_people == 'created_at') {
                            $this->insert_impediment['created_at_persona'] = $this->formatDate($value_people);
                            continue;
                        }

                        if ($key_people == 'updated_at') {
                            $this->insert_impediment['updated_at_persona'] = $this->formatDate($value_people);
                            continue;
                        }

                        $this->insert_impediment[$key_people] = $value_people;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | PADRES
                |--------------------------------------------------------------------------
                */

                if ($ImImpedimento->people->people_fathers) {

                    foreach ($ImImpedimento->people->people_fathers->getAttributes() as $key_fathers => $value_fathers) {

                        if (in_array($key_fathers, $this->ImImpedimentPhatersBitacora)) {

                            if ($key_fathers == 'created_at') {
                                $this->insert_impediment['created_at_padres'] = $this->formatDate($value_fathers);
                                continue;
                            }

                            if ($key_fathers == 'updated_at') {
                                $this->insert_impediment['updated_at_padres'] = $this->formatDate($value_fathers);
                                continue;
                            }

                            $this->insert_impediment[$key_fathers] = $value_fathers;
                        }
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | BAJA
            |--------------------------------------------------------------------------
            */

            if ($ImImpedimento->low) {

                foreach ($ImImpedimento->low->getAttributes() as $key_low => $value_low) {

                    if ($key_low == 'created_at') {
                        $this->insert_impediment['created_at_baja'] = $this->formatDate($value_low);
                        continue;
                    }

                    if ($key_low == 'updated_at') {
                        $this->insert_impediment['updated_at_baja'] = $this->formatDate($value_low);
                        continue;
                    }

                    $this->insert_impediment[$key_low] = $value_low;
                }

                $this->insert_impediment['id_usuario_alta_baja'] = $ImImpedimento->low->id_usuario_alta;
                $this->insert_impediment['id_usuario_modificacion_baja'] = $ImImpedimento->low->id_usuario_modificacion;
                $this->insert_impediment['is_active'] = true;
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR BITACORA
            |--------------------------------------------------------------------------
            */

            ImImpedimentoBitacora::create($this->insert_impediment);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Error en save_binnacle_impediment', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'code'    => $e->getCode(),
            ]);

            throw $e;
        }
    }


    private function formatDate($date)
    {

        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

}
