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
        Schema::create('im_usuario_perfil', function (Blueprint $table) {
            $table->integer('id_usuario');
            $table->smallInteger('id_perfil');
            $table->boolean('bol_eliminado')->default(false);
            $table->timestamp('fec_alta', 6)->useCurrent();
            $table->timestamp('fec_modificacion', 6)->nullable();
            $table->string('usuario_alta', 70);
            $table->string('usuario_modificacion', 70)->nullable();

            $table->primary(['id_usuario', 'id_perfil']);
            $table->timestamps();

            $table->foreign('id_usuario')
            ->references('id')
            ->on('users')
            ->onDelete('restrict');

            $table->foreign('id_perfil')
            ->references('id_perfil')
            ->on('im_cat_perfil')
            ->onDelete('restrict');
        });

        DB::statement("COMMENT ON TABLE im_usuario_perfil IS 'Asignación de perfiles a usuarios';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_usuario_perfil');
    }
};
