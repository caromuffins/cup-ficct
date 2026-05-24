<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->integer('cupo_maximo')->default(80);
            $table->boolean('activa')->default(true);
        });

        Schema::table('materias', function (Blueprint $table) {
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
        });

        Schema::table('gestiones', function (Blueprint $table) {
            $table->integer('anio');
            $table->enum('periodo', ['primero', 'segundo']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activa')->default(false);
            $table->integer('cupo_por_carrera')->default(80);
            $table->decimal('monto_inscripcion', 8, 2)->default(0);
        });

        Schema::table('docentes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ci')->unique();
            $table->string('telefono')->nullable();
            $table->string('especialidad')->nullable();
            $table->integer('max_grupos')->default(4);
            $table->boolean('activo')->default(true);
        });

        Schema::table('postulantes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ci')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('colegio')->nullable();
            $table->string('ciudad')->nullable();
            $table->enum('estado', ['pendiente','habilitado','inscrito','admitido','rechazado'])->default('pendiente');
        });

        Schema::table('requisitos', function (Blueprint $table) {
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('obligatorio')->default(true);
            $table->string('tipo_archivo')->nullable();
            $table->boolean('activo')->default(true);
        });

        Schema::table('inscripciones', function (Blueprint $table) {
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->foreignId('carrera_primera_id')->constrained('carreras');
            $table->foreignId('carrera_segunda_id')->constrained('carreras');
            $table->enum('estado', ['pendiente','pagada','anulada'])->default('pendiente');
            $table->timestamp('fecha_inscripcion')->useCurrent();
        });

        Schema::table('requisito_postulante', function (Blueprint $table) {
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('requisito_id')->constrained('requisitos')->onDelete('cascade');
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->string('archivo_path')->nullable();
            $table->enum('estado', ['pendiente','aprobado','rechazado'])->default('pendiente');
            $table->timestamp('fecha_entrega')->nullable();
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->decimal('monto', 8, 2);
            $table->string('moneda')->default('USD');
            $table->string('metodo')->default('paypal');
            $table->enum('estado', ['pendiente','completado','fallido','reembolsado'])->default('pendiente');
            $table->string('transaccion_id')->nullable()->unique();
            $table->timestamp('fecha_pago')->nullable();
        });

        Schema::table('aulas', function (Blueprint $table) {
            $table->string('nombre');
            $table->string('edificio')->nullable();
            $table->integer('capacidad')->default(70);
            $table->boolean('disponible')->default(true);
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('turno', ['maniana','tarde','noche'])->default('maniana');
            $table->integer('cupo_maximo')->default(70);
            $table->integer('cupo_actual')->default(0);
        });

        Schema::table('asignacion_docentes', function (Blueprint $table) {
            $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
        });

        Schema::table('horarios', function (Blueprint $table) {
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
            $table->foreignId('aula_id')->constrained('aulas')->onDelete('cascade');
            $table->enum('dia', ['lunes','martes','miercoles','jueves','viernes','sabado']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
        });

        Schema::table('examenes', function (Blueprint $table) {
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo', ['parcial1','parcial2','final']);
            $table->integer('puntaje_maximo');
            $table->date('fecha')->nullable();
        });

        Schema::table('notas', function (Blueprint $table) {
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('examen_id')->constrained('examenes')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->decimal('puntaje', 5, 2)->default(0);
            $table->timestamp('fecha_registro')->useCurrent();
        });

        Schema::table('resultado_materias', function (Blueprint $table) {
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->decimal('total_parcial1', 5, 2)->default(0);
            $table->decimal('total_parcial2', 5, 2)->default(0);
            $table->decimal('total_final', 5, 2)->default(0);
            $table->decimal('total', 5, 2)->default(0);
            $table->boolean('aprobado')->default(false);
        });

        Schema::table('admisiones', function (Blueprint $table) {
            $table->foreignId('postulante_id')->constrained('postulantes')->onDelete('cascade');
            $table->foreignId('gestion_id')->constrained('gestiones')->onDelete('cascade');
            $table->foreignId('carrera_asignada_id')->constrained('carreras');
            $table->decimal('promedio_general', 5, 2)->default(0);
            $table->boolean('admitido')->default(false);
            $table->enum('opcion_asignada', ['primera','segunda'])->nullable();
            $table->timestamp('fecha_publicacion')->nullable();
        });
    }

    public function down(): void
    {
        //
    }
};