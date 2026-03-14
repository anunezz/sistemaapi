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
        Schema::create('im_solicitud_documento', function (Blueprint $table) {
            $table->id('id_solicitud_documento'); // bigserial
            $table->unsignedBigInteger('id_solicitud');
            $table->integer('id_cat_anexos')->unsigned()->nullable();
            $table->string('identificador_documento')->nullable();
            $table->date('fecha_documento')->nullable();
            $table->string('url_documento', 150)->nullable();
            $table->boolean('bol_eliminado')->default(false);
            $table->longText('observaciones')->nullable();
            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->timestamps();

            $table->foreign('id_solicitud')->references('id_solicitud')->on('im_solicitud')->onDelete('restrict');
            $table->foreign('id_cat_anexos')->references('id_cat_anexos')->on('im_cat_anexos')->onDelete('cascade');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });

        DB::statement("COMMENT ON TABLE im_solicitud_documento IS 'Datos de documentos asociados a una solicitud';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_solicitud_documento');
    }
};
