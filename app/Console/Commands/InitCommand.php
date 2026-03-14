<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa el proyecto con todas las configuraciones necesarias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando configuración del proyecto...');

        try {
            // 1. Generar clave de aplicación
            $this->info('📑 Generando clave de aplicación...');
            $this->callWithOutput('key:generate', ['--force' => true]);

            // 2. Migrar y sembrar base de datos
            $this->info('🗃️ Configurando base de datos...');
            $this->callWithOutput('migrate:fresh', ['--seed' => true, '--force' => true]);

            // 3. Generar claves de Passport
            $this->info('🔐 Generando claves de Passport...');
            $this->callWithOutput('passport:keys', ['--force' => true]);

            // 4. Crear cliente personal de Passport
            $this->info('👤 Creando cliente personal de Passport...');
            $this->call('passport:client', [
                '--personal' => true,
                '--name' => config('app.name', 'Laravel') . ' Personal Access Client',
                '--provider' => 'users'
            ]);

            // 5. Limpiar cachés
            $this->info('🧹 Limpiando cachés...');
            $this->callWithOutput('cache:clear');
            $this->callWithOutput('config:clear');
            $this->callWithOutput('route:clear');
            $this->callWithOutput('view:clear');
            $this->callWithOutput('optimize:clear');


            $this->newLine();
            $this->info('✅ ¡Proyecto inicializado correctamente!');
            $this->line('🌐 La aplicación está lista para usar.');

        } catch (\Exception $e) {
            $this->error('❌ Error durante la inicialización: ' . $e->getMessage());
            $this->line('Revisa los logs para más detalles.');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Ejecuta un comando Artisan y muestra su salida
     */
    private function callWithOutput(string $command, array $parameters = [])
    {
        $this->line("   Ejecutando: php artisan {$command}");

        $exitCode = Artisan::call($command, $parameters);

        // Mostrar la salida del comando si hay alguna
        $output = Artisan::output();
        if (!empty(trim($output))) {
            $this->line('   ' . trim($output));
        }

        if ($exitCode !== 0) {
            throw new \Exception("El comando '{$command}' falló con código de salida {$exitCode}");
        }

        $this->line('   ✓ Completado');
    }
}
