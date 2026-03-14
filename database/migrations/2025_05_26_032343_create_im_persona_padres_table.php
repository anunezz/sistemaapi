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
        Schema::create('im_persona_padres', function (Blueprint $table) {
            $table->unsignedBigInteger('id_persona');
            $table->string('nombres_padre', 30)->nullable();
            $table->string('primer_apellido_padre', 30)->nullable();
            $table->string('segundo_apellido_padre', 30)->nullable();
            $table->string('nombres_madre', 30)->nullable();
            $table->string('primer_apellido_madre', 30)->nullable();
            $table->string('segundo_apellido_madre', 30)->nullable();
            $table->boolean('bol_eliminado')->default(false);
            // $table->timestamp('fec_alta', 6)->useCurrent();
            // $table->timestamp('fec_modificacion', 6)->nullable();
            $table->unsignedInteger('id_usuario_alta')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->unsignedInteger('id_usuario_modificacion')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->timestamps();
            $table->primary('id_persona');

            $table->foreign('id_persona')
                ->references('id_persona')
                ->on('im_persona')
                ->onDelete('cascade');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });

        DB::statement("COMMENT ON TABLE im_persona_padres IS 'Datos de los padres de una persona si aplica';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_persona_padres');
    }
};
