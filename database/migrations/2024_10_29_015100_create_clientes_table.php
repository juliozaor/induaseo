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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documentos')->onDelete('cascade');
            $table->string('numero_documento')->unique();
            $table->foreignId('ciudad_id')->constrained('ciudades')->onDelete('cascade');
            $table->foreignId('creador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('actualizador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('sector_economico_id')->constrained('sectores_economicos')->onDelete('cascade');
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('correo')->nullable();
            $table->bigInteger('celular')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
