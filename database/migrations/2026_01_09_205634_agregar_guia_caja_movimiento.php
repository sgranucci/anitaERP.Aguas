<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('caja_movimiento', function (Blueprint $table) {
            $table->unsignedBigInteger('guia_id')->nullable()->after('cliente_id');
            $table->foreign('guia_id', 'fk_caja_movimiento_guia')->references('id')->on('guia')->onDelete('restrict')->onUpdate('restrict');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caja_movimiento', function (Blueprint $table) {
            $table->dropForeign('fk_caja_movimiento_guia');
            $table->dropColumn('guia_id');
        });
    }
};
