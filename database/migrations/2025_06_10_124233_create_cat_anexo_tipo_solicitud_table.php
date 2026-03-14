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
        Schema::create('cat_anexo_tipo_solicitud', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cat_anexos')->unsigned()->nullable();
            $table->foreign('id_cat_anexos')->references('id_cat_anexos')->on('im_cat_anexos')->onDelete('cascade');
            $table->integer('id_tipo_solicitud')->unsigned()->nullable();
            $table->foreign('id_tipo_solicitud')->references('id_tipo_solicitud')->on('im_cat_tipo_solicitud')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_anexo_tipo_solicitud');
    }
};
