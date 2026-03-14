<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('im_bitacora_solicitud', function (Blueprint $table) {
            $table->id('id_bitacora_solicitud'); // bigserial
            $table->unsignedBigInteger('id_solicitud')->nullable(); // también bigserial pero sin FK
            $table->date('fecha_registro')->nullable();
            $table->unsignedBigInteger('id_persona')->nullable();
            $table->string('correo_electronico', 70)->nullable();
            $table->char('id_estatus_solicitud', 1)->nullable();
            $table->longText('motivacion_acto_juridico')->nullable();
            $table->smallInteger('id_causal_impedimento')->nullable();
            $table->smallInteger('id_subcausal_impedimento')->nullable();
            $table->integer('id_oficina')->nullable();
            $table->string('anexo_expediente_integrado', 150)->nullable();
            $table->string('anexo_dictamen_verificacion', 150)->nullable();
            $table->string('numero_pasaporte_cancelado', length: 15)->nullable();
            $table->string('otro_documento_soporte', 150)->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->boolean('bol_eliminado')->default(value: false);
            $table->timestamp('fec_alta', 6)->useCurrent();
            $table->string('usuario_alta', 70);
            $table->timestamps();
        });

        DB::statement("COMMENT ON TABLE im_bitacora_solicitud IS 'Datos como estaban en una solicitud antes de realizar un cambio o cambio de estatus';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_bitacora_solicitud');
    }
};
