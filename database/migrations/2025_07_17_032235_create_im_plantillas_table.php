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
        Schema::create('im_plantillas', function (Blueprint $table) {
            $table->id('id_plantilla');
            $table->longText('plantilla')->nullable();
            $table->integer('id_subcausal_impedimento')->unique()->unsigned()->nullable();
            $table->boolean('bol_eliminado')->default(false);
            $table->timestamps();
            $table->foreign('id_subcausal_impedimento')->references('id_subcausal_impedimento')->on('im_cat_subcausal_impedimento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_plantillas');
    }
};
