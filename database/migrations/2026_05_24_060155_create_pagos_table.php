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
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
        $table->decimal('monto', 8, 2);
        $table->string('moneda')->default('USD');
        $table->string('metodo')->default('paypal');
        $table->enum('estado', ['pendiente', 'completado', 'fallido', 'reembolsado'])->default('pendiente');
        $table->string('transaccion_id')->nullable()->unique();
        $table->timestamp('fecha_pago')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
