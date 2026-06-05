<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('titulo_profesional')->nullable()->after('especialidad');
            $table->boolean('tiene_maestria')->default(false)->after('titulo_profesional');
            $table->string('area_maestria')->nullable()->after('tiene_maestria');
            $table->boolean('tiene_diplomado')->default(false)->after('area_maestria');
            $table->string('area_diplomado')->nullable()->after('tiene_diplomado');
            $table->enum('estado_contratacion', ['pendiente', 'contratado', 'rechazado'])->default('pendiente')->after('area_diplomado');
        });
    }

    public function down()
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn([
                'titulo_profesional',
                'tiene_maestria',
                'area_maestria',
                'tiene_diplomado',
                'area_diplomado',
                'estado_contratacion',
            ]);
        });
    }
};
