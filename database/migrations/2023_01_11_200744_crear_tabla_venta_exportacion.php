<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaVentaExportacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta_exportacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('venta_id');
            $table->foreign('venta_id', 'fk_venta_exportacion_venta')->references('id')->on('venta')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('incoterm_id');
            $table->foreign('incoterm_id', 'fk_venta_exportacion_incoterm')->references('id')->on('incoterm')->onDelete('restrict')->onUpdate('restrict');
            $table->string('leyenda',255)->nullable();
            $table->string('condicionventaexportacion',255)->nullable();
            $table->string('formapagoexportacion',255)->nullable();
            $table->string('mercaderiaexportacion',255)->nullable();
            $table->string('monedaexportacion',10)->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('venta_exportacion');
    }
}
