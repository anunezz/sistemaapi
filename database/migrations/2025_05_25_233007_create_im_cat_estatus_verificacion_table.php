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
        Schema::create('im_cat_estatus_verificacion', function (Blueprint $table) {
            $table->id('id_estatus_verificacion');
            $table->string('estatus')->nullable();
            $table->integer('bol_estatus')->nullable();
            $table->boolean('bol_eliminado')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_cat_estatus_verificacion');
    }
};
