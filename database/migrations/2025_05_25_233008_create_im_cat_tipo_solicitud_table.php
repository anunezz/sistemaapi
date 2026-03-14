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
        Schema::create('im_cat_tipo_solicitud', function (Blueprint $table) {
            $table->id('id_tipo_solicitud');
            $table->string('tipo_solicitud', 100);  
            $table->boolean('bol_eliminado')->nullable()->default(false);
            $table->unsignedInteger('id_usuario_alta')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->unsignedInteger('id_usuario_modificacion')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->timestamps();

            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_cat_tipo_impedimento');
    }
};
