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
    Schema::create('grupos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
        $table->string('nombre');
        $table->enum('turno', ['maniana', 'tarde', 'noche'])->default('maniana');
        $table->integer('cupo_maximo')->default(70);
        $table->integer('cupo_actual')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
