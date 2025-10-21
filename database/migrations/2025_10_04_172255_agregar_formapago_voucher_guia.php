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
        Schema::table('voucher_guia', function (Blueprint $table) {
			$table->unsignedBigInteger('formapago_id')->nullable()->after('guia_id');
            $table->foreign('formapago_id', 'fk_voucher_guia_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_guia', function (Blueprint $table) {
            $table->dropForeign('fk_voucher_guia_formapago');
            $table->dropColumn('formapago_id');
        });
    }
};
