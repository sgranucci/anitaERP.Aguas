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
            array('id' => '316', 'nombre' => 'Crear salidas', 'slug' => 'crear-salidas', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '317', 'nombre' => 'Listar salidas', 'slug' => 'listar-salidas', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '318', 'nombre' => 'Editar salidas', 'slug' => 'editar-salidas', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '319', 'nombre' => 'Actualizar salidas', 'slug' => 'editar-salidas', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '320', 'nombre' => 'Borrar salidas', 'slug' => 'borrar-salidas', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
