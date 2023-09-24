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
            array('id' => '324', 'nombre' => 'Crear precios', 'slug' => 'crear-precios', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '325', 'nombre' => 'Listar precios', 'slug' => 'listar-precios', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '326', 'nombre' => 'Editar precios', 'slug' => 'editar-precios', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '327', 'nombre' => 'Actualizar precios', 'slug' => 'actualizar-precios', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '328', 'nombre' => 'Borrar precios', 'slug' => 'borrar-precios', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
