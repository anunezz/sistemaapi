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
        Schema::create('im_cat_pais', function (Blueprint $table) {
            $table->bigIncrements('id_pais'); //TODO SE CAMBIA A AUTOINCREMENTABLE
            $table->char('idalpha2', 2);
            $table->char('idalpha3', 3);
            $table->string('cad_nombre_es', 255);
            $table->boolean('bol_eliminado')->default(false);
            //$table->timestamp('fec_alta')->useCurrent()->nullable();
            //$table->timestamp('fec_modificacion')->nullable();
            $table->unsignedInteger('id_usuario_alta')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->unsignedInteger('id_usuario_modificacion')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->timestamps();

            // $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            // $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('im_cat_oficina', function (Blueprint $table) {
            $table->foreign('id_oficina_padre')
                ->references('id_oficina')
                ->on('im_cat_oficina')
                ->onDelete('restrict');

                $table->foreign('id_pais')
                ->references('id_pais')
                ->on('im_cat_pais')
                ->onDelete('restrict');

            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');

        });

        DB::statement("COMMENT ON TABLE im_cat_pais IS 'Catálogo de paises tomado de SUET.';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_cat_pais');
    }
};
