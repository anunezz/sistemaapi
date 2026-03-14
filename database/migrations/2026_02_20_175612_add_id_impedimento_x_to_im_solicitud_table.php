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
        Schema::table('im_solicitud', function (Blueprint $table) {
            $table->integer('id_impedimento')->unsigned()->nullable();
            $table->foreign('id_impedimento')->references('id_impedimento')->on('im_impedimento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('im_solicitud', function (Blueprint $table) {
            $table->dropForeign(['id_impedimento']);
            $table->dropColumn('id_impedimento');
        });
    }
};
