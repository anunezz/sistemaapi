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
        Schema::create('im_cat_oficina', function (Blueprint $table) {
            $table->bigIncrements('id_oficina'); //TODO SE CAMBIA A AUTOINCREMENTABLE
            $table->string('cad_oficina', 250);
            $table->string('nombre_corto', 191);
            $table->unsignedInteger('id_oficina_padre')->nullable(); //TODO SE CAMBIA unsignedInteger
            $table->unsignedInteger('id_tipo_oficina')->nullable(); //TODO SE CAMBIA unsignedInteger
            $table->unsignedInteger('id_pais'); //TODO SE CAMBIA unsignedInteger
            $table->unsignedInteger('id_jurisdiccion')->nullable();//TODO SE CAMBIA unsignedInteger
            $table->boolean('bol_imprime_pasaporte')->default(true);
            $table->boolean('bol_emite_tramites')->default(true);
            $table->boolean('ome')->default(false);
            $table->unsignedInteger('id_unidad_administrativa')->nullable(); //TODO SE CAMBIA unsignedInteger
            $table->unsignedInteger('id_region')->nullable(); //TODO SE CAMBIA unsignedInteger
            $table->unsignedInteger('id_continente')->nullable(); //TODO SE CAMBIA unsignedInteger
            $table->boolean('bol_no_valida')->default(false);
            $table->boolean('bol_eliminado')->default(false);
            //$table->timestamp('fec_alta')->useCurrent()->nullable();
            //$table->timestamp('fec_modificacion')->nullable();
            $table->unsignedInteger('id_usuario_alta')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->unsignedInteger('id_usuario_modificacion')->nullable(); //TODO SE CAMBIA STRING DE USUARIO POR ID
            $table->timestamps();
            $table->unsignedInteger('id_oficina_suet')->nullable(); //TODO SE AGREGA ID OFICINA SUET
            $table->boolean('bol_activo')->default(false);
            $table->string('correo_electronico', length: 70)->nullable();            
            //$table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            //$table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });



        DB::statement("COMMENT ON TABLE im_cat_oficina IS 'Oficina espejo de SUET, con dato extra. Debe actualizarse junto con SUET';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_cat_oficina');
    }
};
