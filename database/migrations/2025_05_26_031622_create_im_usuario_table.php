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
        Schema::create('im_usuario', function (Blueprint $table) {
            $table->integer('id_usuario')->primary();
            $table->char('usuario', 80);
            $table->char('nombre',80);
            $table->char('primer_apellido');
            $table->char('segundo_apellido')->nullable();
            $table->char('acronimo')->nullable();
            $table->char('email')->nullable();
            $table->boolean('usuario_directorio_activo')->default(true);
            $table->char('autenticacion', 70)->nullable();
            $table->boolean('bol_eliminado')->default(false);
            $table->integer('id_oficina')->nullable();
            $table->timestamp('fec_alta', 6)->useCurrent();
            $table->timestamp('fec_modificacion', 6)->nullable();
            //$table->string('usuario_alta', 70);
            //$table->string('usuario_modificacion', 70)->nullable();
            $table->string('id_usuario_alta', 70);//TODO: Se modifico por que debe ser id del usuario
            $table->string('id_usuario_modificacion', 70)->nullable();//TODO: Se modifico por que debe ser id del usuario
            $table->timestamps();

            $table->foreign('id_oficina')
            ->references('id_oficina')
            ->on('im_cat_oficina')
            ->onDelete('restrict');
        });

        DB::statement("COMMENT ON TABLE im_usuario IS 'Usuarios de sistema';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('im_usuario');
    }
};
