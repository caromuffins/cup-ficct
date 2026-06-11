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
    Schema::create('requisito_postulante', function (Blueprint $table) {
        $table->id();
        $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
        $table->foreignId('requisito_id')->constrained('requisitos')->onDelete('cascade');
        $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
        $table->string('archivo_path')->nullable();
        $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
        $table->timestamp('fecha_entrega')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisito_postulante');
    }
};
