<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('patologias', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }

    public function down()
    {
        Schema::table('patologias', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('descripcion');
        });
    }
};