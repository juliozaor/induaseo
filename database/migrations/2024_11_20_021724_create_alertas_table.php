<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertasTable extends Migration
{
    public function up()
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_hora_activacion');
            $table->foreignId('turno_id')->constrained('turnos');
            $table->integer('total_actividades');
            $table->integer('actividades_finalizadas');
            $table->boolean('estado_turno');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas');
    }
}