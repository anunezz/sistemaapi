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
        Schema::create('im_asignacion_solicitudes', function (Blueprint $table) {
            $table->id('id_asignacion_solicitudes');
            $table->integer('id_solicitud')->unsigned()->nullable();
            $table->integer('id_usuario')->unsigned()->nullable();

            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_solicitud')->references('id_solicitud')->on('im_solicitud')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_asignacion_solicitudes');
    }
};
