<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gestiones', function (Blueprint $table) {
            $table->boolean('publicada')->default(false)->after('activa');
        });
    }

    public function down()
    {
        Schema::table('gestiones', function (Blueprint $table) {
            $table->dropColumn('publicada');
        });
    }
};
