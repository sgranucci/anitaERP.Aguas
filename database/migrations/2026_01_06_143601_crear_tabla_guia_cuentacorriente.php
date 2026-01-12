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
        Schema::create('guia_cuentacorriente', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedBigInteger('guia_id');
            $table->foreign('guia_id', 'fk_guia_cuentacorriente_guia')->references('id')->on('guia')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('rendicionreceptivo_id')->nullable();
            $table->foreign('rendicionreceptivo_id', 'fk_guia_cuentacorriente_rendicionreceptivo')->references('id')->on('rendicionreceptivo')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('caja_movimiento_id')->nullable();
            $table->foreign('caja_movimiento_id', 'fk_guia_cuentacorriente_caja_movimiento')->references('id')->on('caja_movimiento')->onDelete('restrict')->onUpdate('restrict');
            $table->decimal('monto',20,6);
            $table->unsignedBigInteger('moneda_id');
			$table->foreign('moneda_id', 'fk_guia_cuentacorriente_moneda')->references('id')->on('moneda')->onUpdate('restrict')->onDelete('restrict');
            $table->float('cotizacion');
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guia_cuentacorriente');
    }
};
