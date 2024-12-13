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
        Schema::create('sedes_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('insumo_id')->constrained('insumos');
            $table->integer('cantidad');
            $table->boolean('estado');
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
        Schema::dropIfExists('sedes_insumos');
    }
};
