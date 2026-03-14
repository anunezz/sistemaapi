<?php

use App\Models\Catalogs\ImCatPais;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 30);
            $table->string('name',30);
            $table->string('first_name', 30);
            $table->string('second_name', 30)->nullable();
            $table->string('email', 70);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('usuario_directorio_activo')->default(true);
            $table->boolean('bol_eliminado')->default(false);
            $table->integer('id_oficina')->nullable();
            $table->string('puesto', 250)->nullable();
            //$table->string('usuario_alta', 70);
            //$table->string('usuario_modificacion', 70)->nullable();
            $table->integer('id_usuario_alta')->nullable();//TODO: Se modifico por que debe ser id del usuario
            $table->integer('id_usuario_modificacion')->nullable();//TODO: Se modifico por que debe ser id del usuario
            $table->rememberToken();
            $table->timestamps();

             $table->foreign('id_oficina')
            ->references('id_oficina')
            ->on('im_cat_oficina')
            ->onDelete('restrict');



            //$table->softDeletes();
        });

            // 🔒 UNIQUE case-insensitive para username
        DB::statement("
            CREATE UNIQUE INDEX users_username_lower_unique
            ON users (LOWER(username))
        ");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

         Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_usuario_alta')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('id_usuario_modificacion')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

        /*  Schema::table('im_cat_pais', function (Blueprint $table) {
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        }); */

        /* Schema::table('im_cat_oficina', function (Blueprint $table) {
            $table->foreign('id_usuario_alta')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('id_usuario_modificacion')->references('id')->on('users')->onDelete('restrict');
        }); */

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      /*   Schema::table('im_cat_pais', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_alta']);
            $table->dropForeign(['id_usuario_modificacion']);
            $table->dropColumn(['id_usuario_alta', 'id_usuario_modificacion']);
        });
        Schema::table('im_cat_oficina', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_alta']);
            $table->dropForeign(['id_usuario_modificacion']);
            $table->dropColumn(['id_usuario_alta', 'id_usuario_modificacion']);
        }); */
        DB::statement("DROP INDEX IF EXISTS users_username_lower_unique");
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

    }
};
