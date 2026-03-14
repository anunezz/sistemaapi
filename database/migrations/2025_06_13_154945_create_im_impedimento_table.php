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
        Schema::create('im_impedimento', function (Blueprint $table) {
            $table->id('id_impedimento');
            $table->integer('id_persona')->unsigned()->nullable();
            $table->unsignedInteger('id_oficina');
            $table->string('correo_electronico', 70)->nullable();
            $table->string('numero_documento', 500)->nullable();
            $table->integer('id_estatus_impedimento')->unsigned()->nullable();
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
            $table->jsonb('backup_anterior')->nullable();


            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_oficina')->references('id_oficina')->on('im_cat_oficina')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_causal_impedimento')->references('id_causal_impedimento')->on('im_cat_causal_impedimento')->onDelete('restrict');
            $table->foreign('id_subcausal_impedimento')->references('id_subcausal_impedimento')->on('im_cat_subcausal_impedimento')->onDelete('restrict');
            $table->foreign('id_estatus_impedimento')->references('id_estatus_solicitud')->on('im_cat_estatus_solicitud')->onDelete('restrict');
            $table->foreign('id_persona')->references('id_persona')->on('im_persona')->onDelete('cascade');
            $table->foreign('id_usuario_elaboro')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_reviso')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_autorizo')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_usuario_altas')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_impedimento');
    }
};
