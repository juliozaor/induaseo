<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMantenimientosTable extends Migration
{
    public function up()
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->date('ultimo_mtto');
            $table->date('mtto_programado');
            $table->foreignId('estado_id')->constrained('estados');
            $table->text('observaciones_reportadas');
            $table->foreignId('sede_activo_id')->constrained('sedes_activos');
            $table->text('observaciones');
            $table->boolean('estado');
            $table->foreignId('creador_id')->constrained('usuarios');
            $table->foreignId('actualizador_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mantenimientos');
    }
}
