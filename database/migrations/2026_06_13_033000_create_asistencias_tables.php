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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->date('fecha');
            $table->timestamps();

            // Garantiza que solo haya un registro de asistencia por grupo, materia y fecha
            $table->unique(['grupo_id', 'materia_id', 'fecha']);
        });

        Schema::create('asistencia_postulante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asistencia_id')->constrained('asistencias')->cascadeOnDelete();
            $table->foreignId('postulante_id')->constrained('postulantes')->cascadeOnDelete();
            $table->enum('estado', ['presente', 'falta', 'licencia']);
            $table->timestamps();

            // Evita duplicados para un mismo alumno en la misma sesión de asistencia
            $table->unique(['asistencia_id', 'postulante_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_postulante');
        Schema::dropIfExists('asistencias');
    }
};
