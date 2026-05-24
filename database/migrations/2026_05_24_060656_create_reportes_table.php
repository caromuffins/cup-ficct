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
    Schema::create('reportes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
        $table->enum('tipo', ['aprobados', 'grupos', 'docentes', 'admitidos']);
        $table->enum('formato', ['pdf', 'excel', 'html']);
        $table->foreignId('generado_por')->constrained('users');
        $table->string('ruta_archivo')->nullable();
        $table->timestamp('fecha_generacion')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
