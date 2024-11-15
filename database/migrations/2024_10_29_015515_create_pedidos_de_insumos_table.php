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
        Schema::create('pedidos_de_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->integer('cantidad');
            $table->foreignId('centro_trabajo_id')->constrained('centros_de_trabajo')->onDelete('cascade');
            $table->date('fecha_pedido');
            $table->enum('estado', ['pendiente', 'enviado', 'recibido']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos_de_insumos');
    }
};
