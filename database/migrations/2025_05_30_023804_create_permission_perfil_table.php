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
        Schema::create('permission_perfil', function (Blueprint $table) {
            $table->foreignId('id_permission')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreignId('id_perfil')->references('id_perfil')->on('im_cat_perfil')->onDelete('cascade');
            $table->primary(['id_permission', 'id_perfil']); // Clave primaria compuesta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_perfil');
    }
};
