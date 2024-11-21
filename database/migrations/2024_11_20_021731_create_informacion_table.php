<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionTable extends Migration
{
    public function up()
    {
        Schema::create('informacion', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->foreignId('tipo_multimedia_id')->constrained('tipo_multimedias');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->date('fecha');
            $table->string('titulo');
            $table->text('descripcion');
            $table->foreignId('sede_id')->constrained('sedes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('informacion');
    }
}