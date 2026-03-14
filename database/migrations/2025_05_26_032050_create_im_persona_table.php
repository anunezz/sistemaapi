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
        Schema::create('im_persona', function (Blueprint $table) {
            $table->id('id_persona'); // bigserial
            $table->unsignedBigInteger('id_persona_consolidada')->nullable();
            $table->string('nombres', 30);
            $table->string('primer_apellido', 30);
            $table->string('segundo_apellido', 30)->nullable();
            $table->string('correo_electronico', 70)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->char('curp', 21)->nullable();
            $table->integer('id_genero')->unsigned()->nullable();
            $table->string('entidad_federativa_nacimiento', 100)->nullable();
            $table->unsignedInteger('id_pais_nacimiento')->nullable();
            $table->unsignedInteger('id_entidad_federativa_nacimiento')->nullable();
            $table->unsignedInteger('id_municipio_nacimiento')->nullable();
            $table->boolean('bol_eliminado')->default(false);
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();

            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->foreign('id_persona_consolidada')->references('id_persona')->on('im_persona')->onDelete('set null');
            $table->foreign('id_pais_nacimiento')->references('id_pais')->on('im_cat_pais')->onDelete('restrict');
            $table->foreign('id_entidad_federativa_nacimiento')->references('id_entidad_federativa')->on('im_cat_entidad_federativa')->onDelete('restrict');
            $table->foreign('id_municipio_nacimiento')->references('id_municipio')->on('im_cat_municipio')->onDelete('restrict');
            $table->foreign('id_genero')->references('id')->on('im_cat_general_genero')->onDelete('restrict');
        });

        DB::statement("COMMENT ON TABLE im_persona IS 'Datos de una persona ingresada en el sistema de impedimentos';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_persona');
    }
};
