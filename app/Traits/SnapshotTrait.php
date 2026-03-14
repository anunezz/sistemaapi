<?php

namespace App\Traits;

use App\Services\TransactionService;
use Illuminate\Support\Str;

trait SnapshotTrait
{
    /**
     * Carga relaciones y guarda snapshot en bitácora específica.
     *
     * @param \Illuminate\Database\Eloquent\Model $model Modelo con snapshot
     * @param string $bitacoraClass Clase de modelo bitácora
     * @param array $relaciones Relaciones a cargar en el modelo
     * @param array $extraStringsMap Mapeo ['clave_extra' => 'relacion.propiedad']
     * @return void
     */
public function guardarSnapshot(
    $model,
    string $bitacoraClass,
    array $relaciones = [],
    array $extraStrings = []
) {
    // Cargar relaciones si se especifican
    if (!empty($relaciones)) {
        $model->load($relaciones);
    }

    // Procesar snapshotRelationsMap si existe
    if (property_exists($model, 'snapshotRelationsMap')) {
        foreach ($model->snapshotRelationsMap as $relacion => $atributoDestino) {
            $relacionObj = $model->{$relacion};

             // DEBUG ↓↓↓↓↓
        \Log::info("📌 Revisando relación: $relacion");
        if (!$relacionObj) {
            \Log::warning("⚠️ Relación '$relacion' no está cargada o es null.");
        } elseif (!isset($relacionObj->{$atributoDestino})) {
            \Log::warning("⚠️ Relación '$relacion' cargada pero no tiene el atributo '$atributoDestino'");
            \Log::debug("Contenido de '$relacion': " . json_encode($relacionObj));
        } else {
            \Log::info("✅ Relación '$relacion' tiene el atributo '$atributoDestino' con valor: " . $relacionObj->{$atributoDestino});
        }

            if ($relacionObj && isset($relacionObj->{$atributoDestino})) {
                $extraStrings[$atributoDestino] = $relacionObj->{$atributoDestino};
            }
        }
    }

    // Guardar snapshot y retornarlo
    return TransactionService::guardarSnapshotBitacora(
        $model,
        $bitacoraClass,
        $extraStrings
    );
}


}
