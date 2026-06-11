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
    Schema::create('inscripciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
        $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
        $table->foreignId('carrera_primera_id')->constrained('carreras');
        $table->foreignId('carrera_segunda_id')->constrained('carreras');
        $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
        $table->timestamp('fecha_inscripcion')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
