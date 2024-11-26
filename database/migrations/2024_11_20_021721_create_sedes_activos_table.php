<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedesActivosTable extends Migration
{
    public function up()
    {
        Schema::create('sedes_activos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('activo_id')->constrained('activos');
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
        Schema::dropIfExists('sedes_activos');
    }
}