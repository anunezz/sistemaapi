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
        Schema::create('im_solicitud_causales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id');
            $table->foreign('solicitud_id')
                ->references('id_solicitud')->on('im_solicitud')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('id_causal_impedimento');
            $table->unsignedBigInteger('id_subcausal_impedimento');
            $table->foreign('id_causal_impedimento')->references('id_causal_impedimento')->on('im_cat_causal_impedimento');
            $table->foreign('id_subcausal_impedimento')->references('id_subcausal_impedimento')->on('im_cat_subcausal_impedimento');
            $table->timestamps();
            
            $table->unique(['solicitud_id','id_causal_impedimento','id_subcausal_impedimento'], 'uniq_sol_causal_sub');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_solicitud_causales');
    }
};
