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
        Schema::create('im_impedimento_baja', function (Blueprint $table) {
            $table->id('id_secuencial_baja');
            $table->integer('id_impedimento')->unsigned()->nullable();
            $table->date('fecha_elaboracion');
            $table->integer('id_oficina');
            $table->integer('id_estatus_impedimento_baja')->unsigned()->nullable();
            $table->date('fechado')->nullable();
            $table->text('contenido')->nullable();
            $table->string('motivo_levantamiento', 50)->nullable();
            $table->text('descrpción_levantamiento')->nullable();
            $table->string('anexo_expediente_integrado', 150)->nullable();
            $table->string('anexo_dictamen_verificacion', 150)->nullable();
            $table->string('documentacion_complementaria', 150)->nullable();
            $table->string('resolucion_juficial', 150)->nullable();
            $table->string('denuncia_penal_ratificada', 150)->nullable();
            $table->string('otro_documento_baja', 150)->nullable();
            $table->boolean('bol_eliminado')->default(false);
            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_estatus_impedimento_baja')->references('id_estatus_solicitud')->on('im_cat_estatus_solicitud')->onDelete('restrict');
            $table->foreign('id_impedimento')->references('id_impedimento')->on('im_impedimento')->onDelete('cascade');
            $table->foreign('id_oficina')->references('id_oficina')->on('im_cat_oficina')->onDelete('restrict');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_impedimento_baja');
    }
};
