<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagenesSedesActivosTable extends Migration
{
    public function up()
    {
        Schema::create('imagenes_sedes_activos', function (Blueprint $table) {
            $table->id();
            $table->string('imagen');
            $table->foreignId('sede_activo_id')->constrained('sedes_activos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagenes_sedes_activos');
    }
}