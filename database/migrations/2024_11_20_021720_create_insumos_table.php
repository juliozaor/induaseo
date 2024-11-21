<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsumosTable extends Migration
{
    public function up()
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_elemento');
            $table->string('marca');
            $table->string('codigo');
            $table->foreignId('clasificacion_id')->constrained('clasificaciones');
            $table->integer('cantidad');
            $table->foreignId('estado_id')->constrained('estados');
            $table->boolean('estado');
            $table->foreignId('creador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('actualizador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('proveedor');
            $table->string('telefono_proveedor');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('insumos');
    }
}