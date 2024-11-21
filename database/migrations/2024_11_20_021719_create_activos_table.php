<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivosTable extends Migration
{
    public function up()
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_elemento');
            $table->string('marca');
            $table->string('serie');
            $table->foreignId('clasificacion_id')->constrained('clasificaciones');
            $table->integer('cantidad');
            $table->foreignId('estado_id')->constrained('estados');
            $table->boolean('estado');
            $table->foreignId('creador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('actualizador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activos');
    }
}