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
        Schema::create('im_impedimento_documento', function (Blueprint $table) {
            $table->id('id_impedimento_documento'); // bigserial
            $table->unsignedBigInteger('id_impedimento');
            $table->integer('id_cat_anexos')->unsigned()->nullable();
            $table->string('identificador_documento', 250)->nullable();
            $table->date('fecha_documento')->nullable();
            $table->string('url_documento', 150)->nullable();
            $table->boolean('bol_eliminado')->default(false);
            $table->longText('observaciones')->nullable();
            $table->unsignedInteger('id_usuario_alta')->nullable();
            $table->unsignedInteger('id_usuario_modificacion')->nullable();
            $table->timestamps();

            $table->foreign('id_impedimento')->references('id_impedimento')->on('im_impedimento')->onDelete('restrict');
            $table->foreign('id_cat_anexos')->references('id_cat_anexos')->on('im_cat_anexos')->onDelete('cascade');
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_impedimento_documento');
    }
};
