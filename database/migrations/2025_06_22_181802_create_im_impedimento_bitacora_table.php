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
        Schema::create('im_impedimento_bitacora', function (Blueprint $table) {
            $table->id('id_impedimento_bitacora');
            $table->unsignedBigInteger('id_impedimento')->unsigned()->nullable();
            $table->unsignedInteger('id_oficina')->nullable();
            $table->string('correo_electronico', 70)->nullable();
            $table->string('numero_documento', 500)->nullable();
            $table->integer('id_estatus_impedimento')->unsigned()->nullable();
            $table->longText(column: 'cad_oficina')->nullable();
            $table->longText('motivacion_acto_juridico')->nullable();
            $table->unsignedSmallInteger('id_causal_impedimento')->nullable();
            $table->string('causal_otro_descripcion', 100)->nullable();
            $table->unsignedSmallInteger('id_subcausal_impedimento')->nullable();
            $table->string('anexo_expediente_integrado', 150)->nullable();
            $table->string('anexo_dictamen_verificacion', 150)->nullable();
            $table->string('numero_pasaporte_cancelado', 15)->nullable();
            $table->string('otro_documento_soporte', 150)->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->string('numero_impedimento', 500)->nullable();
            $table->boolean('dependencia')->nullable()->default(false);
            $table->string('nombre_dependencia')->nullable();
            $table->date('fecha_autorizacion')->nullable();
            $table->integer('id_usuario_elaboro')->unsigned()->nullable();
            $table->integer('id_usuario_reviso')->unsigned()->nullable();
            $table->integer('id_usuario_autorizo')->unsigned()->nullable();
            $table->integer('id_usuario_altas')->unsigned()->nullable();
            $table->boolean('bol_eliminado')->nullable()->default(false);
            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->timestampTz('created_at_impedimento')->nullable();
            $table->timestampTz('updated_at_impedimento')->nullable();
            //PERSONA
            $table->integer('id_persona')->unsigned()->nullable();
            $table->unsignedBigInteger('id_persona_consolidada')->nullable();
            $table->string('nombres', 30);
            $table->string('primer_apellido', 30);
            $table->string('segundo_apellido', 30)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->char('curp', 21)->nullable();
            $table->integer('id_genero')->unsigned()->nullable();
            $table->string('entidad_federativa_nacimiento', 100)->nullable();
            $table->unsignedInteger('id_pais_nacimiento')->nullable();
            $table->unsignedInteger('id_entidad_federativa_nacimiento')->nullable();
            $table->unsignedInteger('id_municipio_nacimiento')->nullable();
            $table->boolean('bol_eliminado_persona')->nullable()->default(false);
            $table->timestampTz('created_at_persona')->nullable();
            $table->timestampTz('updated_at_persona')->nullable();
            //PADRES
            $table->string('nombres_padre', 30)->nullable();
            $table->string('primer_apellido_padre', 30)->nullable();
            $table->string('segundo_apellido_padre', 30)->nullable();
            $table->string('nombres_madre', 30)->nullable();
            $table->string('primer_apellido_madre', 30)->nullable();
            $table->string('segundo_apellido_madre', 30)->nullable();
            $table->boolean('bol_eliminado_padres')->nullable()->default(false);
            $table->timestampTz('created_at_padres')->nullable();
            $table->timestampTz('updated_at_padres')->nullable();
            //BAJA
            $table->integer('id_secuencial_baja')->unsigned()->nullable();
            $table->date('fecha_elaboracion')->nullable();
            $table->integer('id_estatus_impedimento_baja')->unsigned()->nullable();
            $table->date('fechado')->nullable();
            $table->text('contenido')->nullable();
            $table->string('motivo_levantamiento', 50)->nullable();
            $table->text('descrpción_levantamiento')->nullable();
            $table->string('anexo_expediente_integrado_baja', 150)->nullable();
            $table->string('anexo_dictamen_verificacion_baja', 150)->nullable();
            $table->string('documentacion_complementaria', 150)->nullable();
            $table->string('resolucion_juficial', 150)->nullable();
            $table->string('denuncia_penal_ratificada', 150)->nullable();
            $table->string('otro_documento_baja', 150)->nullable();
            $table->unsignedInteger('id_usuario_alta_baja')->nullable();
            $table->unsignedInteger('id_usuario_modificacion_baja')->nullable();
            $table->boolean('is_active')->nullable()->default(false);
            $table->boolean('bol_eliminado_bajas')->nullable()->default(false);
            $table->timestampTz('created_at_bajas')->nullable();
            $table->timestampTz('updated_at_bajas')->nullable();

            $table->string('estatus_solicitud', 100)->nullable();
            $table->string('tipo_solicitud')->nullable();
            $table->unsignedSmallInteger('id_tipo_solicitud')->nullable();
            $table->longText('causal_impedimento')->nullable();
            $table->string('subcausal_impedimento', 250)->nullable();
            $table->string('usuario_alta', 30)->nullable();
            $table->string('usuario_modificacion', 30)->nullable();


            $table->foreign('id_impedimento')->references('id_impedimento')->on('im_impedimento')->onDelete('restrict');
            $table->foreign('id_oficina')->references('id_oficina')->on('im_cat_oficina')->onDelete('restrict');
            $table->foreign('id_estatus_impedimento')->references('id_estatus_solicitud')->on('im_cat_estatus_solicitud')->onDelete('restrict');
            $table->foreign('id_causal_impedimento')->references('id_causal_impedimento')->on('im_cat_causal_impedimento')->onDelete('restrict');
            $table->foreign('id_usuario_elaboro')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_reviso')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_autorizo')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_altas')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_subcausal_impedimento')->references('id_subcausal_impedimento')->on('im_cat_subcausal_impedimento')->onDelete('restrict');
            $table->foreign('id_tipo_solicitud')->references('id_tipo_solicitud')->on('im_cat_tipo_solicitud')->onDelete('restrict');
            //PERSONA
            $table->foreign('id_persona')->references('id_persona')->on('im_persona')->onDelete('cascade');
            $table->foreign('id_persona_consolidada')->references('id_persona')->on('im_persona')->onDelete('set null');
            $table->foreign('id_pais_nacimiento')->references('id_pais')->on('im_cat_pais')->onDelete('restrict');
            $table->foreign('id_entidad_federativa_nacimiento')->references('id_entidad_federativa')->on('im_cat_entidad_federativa')->onDelete('restrict');
            $table->foreign('id_municipio_nacimiento')->references('id_municipio')->on('im_cat_municipio')->onDelete('restrict');
            $table->foreign('id_genero')->references('id')->on('im_cat_general_genero')->onDelete('restrict');
            //BAJA
            $table->foreign('id_usuario_alta_baja')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion_baja')->references('id')->on('users')->onDelete('restrict');


            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_impedimento_bitacora');
    }
};
