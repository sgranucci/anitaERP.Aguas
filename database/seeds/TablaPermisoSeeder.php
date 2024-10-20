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
            array('id' => '505', 'nombre' => 'Ingresar cotizacion', 'slug' => 'crear-cotizacion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '506', 'nombre' => 'Listar cotizacion', 'slug' => 'listar-cotizacion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '507', 'nombre' => 'Editar cotizacion', 'slug' => 'editar-cotizacion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '508', 'nombre' => 'Actualizar cotizacion', 'slug' => 'actualizar-cotizacion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '509', 'nombre' => 'Borrar cotizacion', 'slug' => 'borrar-cotizacion', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
