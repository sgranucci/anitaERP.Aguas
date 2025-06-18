<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaRendicionreceptivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rendicionreceptivo', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id', 'fk_rendicionreceptivo_empresa')->references('id')->on('empresa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('caja_id');
            $table->foreign('caja_id', 'fk_rendicionreceptivo_caja')->references('id')->on('caja')->onDelete('restrict')->onUpdate('restrict');
            $table->string('serie',1);
            $table->unsignedBigInteger('numerotalonario');
            $table->unsignedBigInteger('guia_id');
            $table->foreign('guia_id', 'fk_rendicionreceptivo_guia')->references('id')->on('guia')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('movil_id');
            $table->foreign('movil_id', 'fk_rendicionreceptivo_movil')->references('id')->on('movil')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
