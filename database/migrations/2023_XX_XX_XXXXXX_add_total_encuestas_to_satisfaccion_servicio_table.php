<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalEncuestasToSatisfaccionServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('satisfaccion_servicio', function (Blueprint $table) {
            $table->unsignedInteger('total_encuestas')->default(0)->after('total_puntos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('satisfaccion_servicio', function (Blueprint $table) {
            $table->dropColumn('total_encuestas');
        });
    }
}
