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
            array('id' => '465', 'nombre' => 'Ingresar comision de servicio terrestre', 'slug' => 'crear-comision-servicio-terrestre', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '466', 'nombre' => 'Listar comision de servicio terrestre', 'slug' => 'listar-comision-servicio-terrestre', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '467', 'nombre' => 'Editar comision de servicio terrestre', 'slug' => 'editar-comision-servicio-terrestre', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '468', 'nombre' => 'Actualizar comision de servicio terrestre', 'slug' => 'actualizar-comision-servicio-terrestre', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '469', 'nombre' => 'Borrar comision de servicio terrestre', 'slug' => 'borrar-comision-servicio-terrestre', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
