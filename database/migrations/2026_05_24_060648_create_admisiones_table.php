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
    Schema::create('admisiones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
        $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
        $table->foreignId('carrera_asignada_id')->constrained('carreras');
        $table->decimal('promedio_general', 5, 2)->default(0);
        $table->boolean('admitido')->default(false);
        $table->enum('opcion_asignada', ['primera', 'segunda'])->nullable();
        $table->timestamp('fecha_publicacion')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admisiones');
    }
};
