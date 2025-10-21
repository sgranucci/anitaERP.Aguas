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
        Schema::table('cuentacaja', function (Blueprint $table) {
			$table->unsignedBigInteger('formapago_id')->nullable()->after('cbu');
            $table->foreign('formapago_id', 'fk_cuentacaja_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuentacaja', function (Blueprint $table) {
            $table->dropForeign('fk_cuentacaja_formapago');
            $table->dropColumn('formapago_id');
        });
    }
};
