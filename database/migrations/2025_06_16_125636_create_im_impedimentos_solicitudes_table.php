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
        Schema::create('im_impedimentos_solicitudes', function (Blueprint $table) {
            $table->id('solicitud_pedimento');
            $table->integer('id_impedimento')->unsigned()->nullable();
            $table->integer('id_solicitud')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('id_impedimento')->references('id_impedimento')->on('im_impedimento')->onDelete('cascade');
            $table->foreign('id_solicitud')->references('id_solicitud')->on('im_solicitud')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_personas_solicitudes');
    }
};
