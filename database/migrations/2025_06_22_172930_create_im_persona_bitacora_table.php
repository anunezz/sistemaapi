<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('im_persona_bitacora', function (Blueprint $table) {
            $table->id('id_persona_bitacora');
            $table->unsignedBigInteger('id_persona')->nullable(); // Referencia al registro original
            $table->unsignedBigInteger('id_persona_consolidada')->nullable();
            $table->string('persona_consolidada', 100)->nullable();

            $table->string('nombres', 30)->nullable();
            $table->string('primer_apellido', 30)->nullable();
            $table->string('segundo_apellido', 30)->nullable();
            $table->string('correo_electronico', 70)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->char('curp', 21)->nullable();
            $table->integer('id_genero')->unsigned()->nullable();
            $table->string('entidad_federativa_nacimiento', 100)->nullable();

            $table->unsignedInteger('id_pais_nacimiento')->nullable();
            // $table->string('pais_nacimiento')->nullable();
            $table->unsignedInteger('id_entidad_federativa_nacimiento')->nullable();
            // $table->string('entidad_federativa_nacimiento_texto')->nullable(); // Evitar colisión
            $table->unsignedInteger('id_municipio_nacimiento')->nullable();
            // $table->string('municipio_nacimiento')->nullable();

                        // Control y trazabilidad
            $table->boolean('bol_eliminado')->default(false);
            $table->unsignedBigInteger('id_usuario_alta')->nullable();
            $table->unsignedBigInteger('id_usuario_modificacion')->nullable();

            // Datos de padres
            $table->string('nombres_padre', 30)->nullable();
            $table->string('primer_apellido_padre', 30)->nullable();
            $table->string('segundo_apellido_padre', 30)->nullable();
            $table->string('nombres_madre', 30)->nullable();
            $table->string('primer_apellido_madre', 30)->nullable();
            $table->string('segundo_apellido_madre', 30)->nullable();
            $table->timestamps();

            // FOREIGN KEYS
            $table->foreign('id_persona')->references('id_persona')->on('im_persona')->onDelete('restrict');
            $table->foreign('id_persona_consolidada')->references('id_persona')->on('im_persona')->onDelete('set null');
            $table->foreign('id_genero')->references('id')->on('im_cat_general_genero')->onDelete('restrict');
            $table->foreign('id_pais_nacimiento')->references('id_pais')->on('im_cat_pais')->onDelete('restrict');
            $table->foreign('id_entidad_federativa_nacimiento')->references('id_entidad_federativa')->on('im_cat_entidad_federativa')->onDelete('restrict');
            $table->foreign('id_municipio_nacimiento')->references('id_municipio')->on('im_cat_municipio')->onDelete('restrict');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('im_persona_bitacora');
    }
};
