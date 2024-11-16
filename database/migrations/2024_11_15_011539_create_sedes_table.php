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
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('ciudad_id')->constrained('ciudades')->onDelete('cascade');
            $table->string('direccion');
            $table->string('telefono');
            $table->time('horario_inicio');
            $table->time('horario_fin');
            $table->boolean('estado')->default(1);
            $table->foreignId('regional_id')->nullable()->constrained('regionales')->onDelete('set null');
            $table->foreignId('creador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('actualizador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
