<?php

namespace Database\Seeders;

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
            array('id' => '535', 'nombre' => 'Ingresa asignacion de caja', 'slug' => 'crea-asignacion-caja', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '536', 'nombre' => 'Lista asignacion de caja', 'slug' => 'lista-asignacion-caja', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '537', 'nombre' => 'Edita asignacion de caja', 'slug' => 'edita-asignacion-caja', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '538', 'nombre' => 'Actualiza asignacion de caja', 'slug' => 'actualiza-asignacion-caja', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '539', 'nombre' => 'Borra asignacion de caja', 'slug' => 'borra-asignacion-caja', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
