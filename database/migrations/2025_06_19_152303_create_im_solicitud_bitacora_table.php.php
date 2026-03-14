<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('im_solicitud_bitacora', function (Blueprint $table) {
            $table->id('id_bitacora'); // ID único de la bitácora
            $table->integer('id_solicitud')->unsigned()->nullable();
            $table->date('fecha_registro')->nullable();
            $table->string('tipo_solicitud', 100)->nullable();
            $table->integer('id_tipo_solicitud')->unsigned()->nullable();
            $table->string('estatus_solicitud', 100)->nullable();
            $table->unsignedSmallInteger('id_estatus_solicitud');
            $table->integer('id_estatus_verificacion')->unsigned()->nullable();
            $table->longText('cad_oficina')->nullable();
            $table->unsignedInteger('id_oficina')->nullable();
            $table->string('prioridad', 250)->nullable();
            $table->integer('id_prioridad')->unsigned()->nullable();
            $table->boolean('urgencia')->default(false);
            $table->string('observaciones', 500)->nullable();
            $table->integer('id_cita')->unsigned()->nullable();
            $table->integer('id_solicitud_suet')->unsigned()->nullable();
            $table->string('nombres', 30)->nullable();
            $table->string('primer_apellido', 30)->nullable();
            $table->string('segundo_apellido', 30)->nullable();
            $table->string('persona_correo_electronico', 70)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('curp', 21)->nullable();
            $table->string('causal_otro_descripcion', 100)->nullable();
            $table->integer('id_genero')->unsigned()->nullable();
            $table->string('entidad_federativa_nacimiento', 250)->nullable();
            $table->integer('id_pais_nacimiento')->unsigned()->nullable();
            $table->string('nombres_padre', 30)->nullable();
            $table->string('primer_apellido_padre', 30)->nullable();
            $table->string('segundo_apellido_padre', 30)->nullable();
            $table->string('nombres_madre', 30)->nullable();
            $table->string('primer_apellido_madre', 30)->nullable();
            $table->string('segundo_apellido_madre', 30)->nullable();
            $table->string('correo_electronico', 70)->nullable();
            $table->longText('motivacion_acto_juridico')->nullable();
            $table->longText('causal_impedimento')->nullable();
            $table->string('nombres_identidad', 30)->nullable();
            $table->string('primer_apellido_identidad', 30)->nullable();
            $table->string('segundo_apellido_identidad', 30)->nullable();
            $table->string('curp_identidad', 21)->nullable();
            $table->unsignedSmallInteger('id_causal_impedimento')->nullable();
            $table->unsignedSmallInteger('id_subcausal_impedimento')->nullable();
            $table->string('anexo_expediente_integrado', 150)->nullable();
            $table->string('anexo_dictamen_verificacion', 150)->nullable();
            $table->string('numero_pasaporte_cancelado', 15)->nullable();
            $table->string('otro_documento_soporte', 150)->nullable();
            $table->string('documentacion_complementaria', 150)->nullable();
            $table->string('resolucion_judicial', 150)->nullable();
            $table->string('nombre_dependencia', 250)->nullable();
            $table->string('numero_documento', 500)->nullable();
            $table->longText('cuerpo_correo')->nullable();
            $table->string('denuncia_penal_ratificada', 150)->nullable();
            $table->boolean('dependencia')->nullable()->default(false);
            $table->boolean('bol_eliminado')->nullable()->default(false);
            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->timestampsTz();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_solicitud_bitacora');
    }
};
