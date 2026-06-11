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
    Schema::create('gestiones', function (Blueprint $table) {
        $table->id();
        $table->integer('anio');
        $table->enum('periodo', ['primero', 'segundo']);
        $table->date('fecha_inicio');
        $table->date('fecha_fin');
        $table->boolean('activa')->default(false);
        $table->integer('cupo_por_carrera')->default(80);
        $table->decimal('monto_inscripcion', 8, 2)->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestiones');
    }
};
