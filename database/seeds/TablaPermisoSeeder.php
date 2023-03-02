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
            array('id' => '311', 'nombre' => 'Crear movimientos de stock', 'slug' => 'crear-movimientos-de-stock', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '312', 'nombre' => 'Listar movimientos de stock', 'slug' => 'listar-movimientos-de-stock', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '313', 'nombre' => 'Editar movimientos de stock', 'slug' => 'editar-movimientos-de-stock', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '314', 'nombre' => 'Actualizar movimientos de stock', 'slug' => 'editar-movimientos-de-stock', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '315', 'nombre' => 'Borrar movimientos de stock', 'slug' => 'borrar-movimientos-de-stock', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
