<?php

namespace App\Traits;

trait CheckSpaceNfsTrait {

    public function getStorageInformation()
    {
        //////////////////Prueba para saber cuanto almacenamiento disponible hay en el disco
        $defaultDisk = config('filesystems.default'); // s3, nfs, etc.

        \Log::info('Disco por defecto: ' . $defaultDisk);

        if ($defaultDisk === "nfs") {
            $pathNFS = config('filesystems.disks.nfs.root');

            // Validar si el path existe
            if (!is_dir($pathNFS)) {
                \Log::error("El path NFS no existe o no es accesible: {$pathNFS}");
                return false;
            }

            // Validar si se puede escribir
            if (!is_writable($pathNFS)) {
                \Log::error("El path NFS existe pero NO es escribible: {$pathNFS}");
                return false;
            }

            try {
                $total = disk_total_space($pathNFS);
                $free = disk_free_space($pathNFS);

                // Convertir a GB
                $totalGb = round($total / 1073741824, 2);
                $freeGb = round($free / 1073741824, 2);

                \Log::info("Espacio total: {$totalGb} GB | Espacio libre: {$freeGb} GB");

                //if ($freeGb < 0.6) { // Menos de 600 MB disponibles
                if ($freeGb < 0.1) { // Menos de 100 MB disponibles
                    \Log::error("Espacio insuficiente en NFS: {$freeGb} GB disponibles.");
                    return false;
                }
            } catch (\Exception $e) {
                \Log::error("Error al consultar el espacio en NFS: " . $e->getMessage());
                return false;
            }
        }

        return true;
    }
}

