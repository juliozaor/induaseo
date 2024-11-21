<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagenesActividadesTable extends Migration
{
    public function up()
    {
        Schema::create('imagenes_actividades', function (Blueprint $table) {
            $table->id();
            $table->string('imagen');
            $table->foreignId('actividad_id')->constrained('actividades');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagenes_actividades');
    }
}