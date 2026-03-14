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
        Schema::create('im_cat_subcausal_impedimento', function (Blueprint $table) {
            $table->bigIncrements('id_subcausal_impedimento');  //TODO SE CAMBIA A AUTOINCREMENTABLE
            $table->unsignedInteger('id_causal_impedimento');
            $table->string('subcausal_impedimento', 120);
            $table->boolean('bol_eliminado')->default(false);
            //$table->timestamp('fec_alta')->useCurrent()->nullable();
            //$table->timestamp('fec_modificacion')->nullable();
            $table->unsignedInteger('id_usuario_alta')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->unsignedInteger('id_usuario_modificacion')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->timestamps();


            $table->foreign('id_causal_impedimento')
                ->references('id_causal_impedimento')
                ->on('im_cat_causal_impedimento')
                ->onDelete('restrict');

            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });

        DB::statement("COMMENT ON TABLE im_cat_subcausal_impedimento IS 'Catálogo de subcausales de impedimento (detalle de una causa para un impedimento derivado de una causal';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_cat_subcausal_impedimento');
    }
};
