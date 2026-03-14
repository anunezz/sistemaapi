<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ImCatEstatusVerificacionSeeder::class,
            ImCatPrioridadesSeeder::class,
            ImCatPaisSeeder::class,
            ImCatEntidadFederativaSeeder::class,
            ImCatMunicipioSeeder::class,
            ImCatOficinaSeeder::class,
            ImCatPerfilSeeder::class,
            ImCatGeneralGeneroSeeder::class,
            ImCatEstatusSolicitudSeeder::class,
            ImCatCausalImpedimentoSeeder::class,
            ImCatTipoSolicitudSedder::class,
            ImCatAnexosSeeder::class,
            UserSeeder::class,
            UsuarioPerfilSeeder::class,
            PermissionSeeder::class,
            PermissionPerfilSeeder::class,
            UserPermissionSeeder::class,
            CatModuleSeeder::class,
            CatTransactionTypesSeeder::class,
            CatAnexoTipoSolicitudSeeder::class,
            ImCatSubcausalImpedimentosSeeder::class,
            ActivarOficinasDesdeTxtSeeder::class
        ]);

        if (\DB::connection()->getName() === 'pgsql') {
            $tablesToCheck = array(
                'users',
            );
            foreach ($tablesToCheck as $tableToCheck) {
                dump('Checking the next id sequence for ' . $tableToCheck);
                $highestId = \DB::table($tableToCheck)->select(\DB::raw('MAX(id)'))->first();
                \DB::select('SELECT setval(\'' . $tableToCheck . '_id_seq\', ' . $highestId->max . ')');

            }
        }
    }
}
