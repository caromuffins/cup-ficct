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
    Schema::create('postulantes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('ci')->unique();
        $table->date('fecha_nacimiento')->nullable();
        $table->string('telefono')->nullable();
        $table->string('colegio')->nullable();
        $table->string('ciudad')->nullable();
        $table->enum('estado', ['pendiente', 'habilitado', 'inscrito', 'admitido', 'rechazado'])->default('pendiente');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulantes');
    }
};
