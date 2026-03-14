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
        Schema::create('im_solicitud', function (Blueprint $table) {
            $table->id('id_solicitud'); // bigserial NOT NULL PRIMARY KEY
            $table->date('fecha_registro')->nullable();
            $table->integer('id_tipo_solicitud')->unsigned()->nullable();
            $table->unsignedSmallInteger('id_estatus_solicitud');
            $table->integer('id_estatus_verificacion')->unsigned()->nullable();
            $table->unsignedInteger('id_oficina');
            $table->integer('id_prioridad')->unsigned()->nullable();
            $table->boolean('urgencia')->default(false);
            $table->string('observaciones', 500)->nullable();
            $table->string('numero_documento', 500)->nullable();
            $table->longText('cuerpo_correo')->nullable();
            $table->integer('id_cita')->unsigned()->nullable();
            $table->integer('id_solicitud_suet')->unsigned()->nullable();
            $table->string('nombres',30)->nullable();
            $table->string('primer_apellido',30)->nullable();
            $table->string('segundo_apellido',30)->nullable();
            $table->string('persona_correo_electronico', 70)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('curp',21)->nullable();
            $table->integer('id_genero')->unsigned()->nullable();
            $table->string('entidad_federativa_nacimiento', 100)->nullable();
            $table->integer('id_pais_nacimiento')->unsigned()->nullable();
            $table->string('nombres_padre', 30)->nullable();
            $table->string('primer_apellido_padre', 30)->nullable();
            $table->string('segundo_apellido_padre', 30)->nullable();
            $table->string('nombres_madre', 30)->nullable();
            $table->string('primer_apellido_madre', 30)->nullable();
            $table->string('segundo_apellido_madre', 30)->nullable();
            $table->string('correo_electronico', 70)->nullable();
            $table->longText('motivacion_acto_juridico')->nullable();
            $table->unsignedSmallInteger('id_causal_impedimento')->nullable();
            $table->string('causal_otro_descripcion', 100)->nullable();
            $table->unsignedSmallInteger('id_subcausal_impedimento')->nullable();
            $table->string('anexo_expediente_integrado', 150)->nullable();
            $table->string('anexo_dictamen_verificacion', 150)->nullable();
            $table->string('numero_pasaporte_cancelado', 15)->nullable();
            $table->string('otro_documento_soporte', 150)->nullable();
            $table->string('documentacion_complementaria', 150)->nullable();
            $table->string('resolucion_judicial', 150)->nullable();
            $table->string('denuncia_penal_ratificada', 150)->nullable();
            $table->boolean('dependencia')->nullable()->default(false);
            $table->string('nombre_dependencia')->nullable();
            $table->string('nombres_identidad')->nullable();
            $table->string('primer_apellido_identidad')->nullable();
            $table->string('segundo_apellido_identidad')->nullable();
            $table->string('curp_identidad')->nullable();
            $table->date('fecha_autorizacion')->nullable();
            $table->integer('id_usuario_elaboro')->unsigned()->nullable();
            $table->integer('id_usuario_reviso')->unsigned()->nullable();
            $table->integer('id_usuario_autorizo')->unsigned()->nullable();
            $table->integer('id_usuario_altas')->unsigned()->nullable();
            $table->boolean('bol_eliminado')->nullable()->default(false);
            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->jsonb('backup_anterior')->nullable();
            $table->jsonb('verificacion_impedimentos')->nullable();
            $table->timestamps();


            $table->foreign('id_prioridad')->references('id_prioridad')->on('im_cat_prioridades')->onDelete('cascade');
            $table->foreign('id_genero')->references('id')->on('im_cat_general_genero')->onDelete('restrict');
            $table->foreign('id_pais_nacimiento')->references('id_pais')->on('im_cat_pais')->onDelete('cascade');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_oficina')->references('id_oficina')->on('im_cat_oficina')->onDelete('restrict');
            $table->foreign('id_estatus_solicitud')->references('id_estatus_solicitud')->on('im_cat_estatus_solicitud')->onDelete('restrict');
            $table->foreign('id_causal_impedimento')->references('id_causal_impedimento')->on('im_cat_causal_impedimento')->onDelete('restrict');
            $table->foreign('id_subcausal_impedimento')->references('id_subcausal_impedimento')->on('im_cat_subcausal_impedimento')->onDelete('restrict');
            $table->foreign('id_tipo_solicitud')->references('id_tipo_solicitud')->on('im_cat_tipo_solicitud')->onDelete('restrict');
            $table->foreign('id_usuario_elaboro')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_reviso')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_autorizo')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_altas')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_solicitud');
    }
};
