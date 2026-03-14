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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250); // Nombre único del permiso (e.g., 'view_users', 'edit_users')
            $table->string('display_name', 250)->nullable(); // Nombre amigable para mostrar en la UI (e.g., 'Ver Usuarios')
            $table->text('description')->nullable(); // Descripción del permiso
            $table->boolean('unassignable')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable(); // Para permisos padre/hijo
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('permissions')
                  ->onDelete('cascade'); // Si se elimina un padre, se eliminan los hijos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
