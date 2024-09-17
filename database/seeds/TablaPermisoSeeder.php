<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablaPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();
        $permisos = [
            array('id' => '480', 'nombre' => 'Ingresar tipo de transaccion compras', 'slug' => 'crear-tipo-transaccion-compra', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '481', 'nombre' => 'Listar tipo de transaccion compras', 'slug' => 'listar-tipo-transaccion-compra', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '482', 'nombre' => 'Editar tipo de transaccion compras', 'slug' => 'editar-tipo-transaccion-compra', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '483', 'nombre' => 'Actualizar tipo de transaccion compras', 'slug' => 'actualizar-tipo-transaccion-compra', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '484', 'nombre' => 'Borrar tipo de transaccion compras', 'slug' => 'borrar-tipo-transaccion-compra', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
