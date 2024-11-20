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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            $table->boolean('estado')->default(1);
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
        Schema::dropIfExists('areas');
    }
};
