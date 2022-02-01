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
            array('id' => '231', 'nombre' => 'Crear pedidos', 'slug' => 'crear-pedidos', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '232', 'nombre' => 'Listar pedidos', 'slug' => 'listar-pedidos', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '233', 'nombre' => 'Editar pedidos', 'slug' => 'editar-pedidos', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '234', 'nombre' => 'Actualizar pedidos', 'slug' => 'editar-pedidos', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '235', 'nombre' => 'Borrar pedidos', 'slug' => 'borrar-pedidos', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
