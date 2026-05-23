<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{

    public function up(){

        Schema::table('municipios', function (Blueprint $table) {
            $table->foreignId('distrito_id')->nullable()->constrained('distritos')->onDelete('set null');
        });
    }

    public function down(){
        
        Schema::table('municipios', function (Blueprint $table) {
            $table->dropForeign(['distrito_id']);
            $table->dropColumn('distrito_id');
        });
    }
};