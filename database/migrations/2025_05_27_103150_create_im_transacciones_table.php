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
        Schema::create('im_transacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cat_module_id')->nullable();
            $table->foreign('cat_module_id')->references('id')->on('im_cat_modulos')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('cat_transaction_type_id');
            $table->foreign('cat_transaction_type_id')->references('id')->on('im_cat_tipos_transacciones');
            //TODO: SE CAMBIO A LONGTEXT 'ACTION' PREGUMNTAR A ADRIAN SI SE CORRERAN MIGRACIONES
            $table->longText('action');
            // $table->string('action', 250);
            $table->json('parameters')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_transacciones');
    }
};
