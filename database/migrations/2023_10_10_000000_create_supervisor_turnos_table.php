
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorTurnosTable extends Migration
{
    public function up()
    {
        Schema::create('supervisor_turnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('sede_id');
            $table->unsignedBigInteger('turno_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();

            $table->foreign('supervisor_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('sede_id')->references('id')->on('sedes')->onDelete('cascade');
            $table->foreign('turno_id')->references('id')->on('turnos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('supervisor_turnos');
    }
}