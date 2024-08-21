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
            array('id' => '426', 'nombre' => 'Ingresar medio de pago', 'slug' => 'crear-medio-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '427', 'nombre' => 'Listar medio de pago', 'slug' => 'listar-medio-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '428', 'nombre' => 'Editar medio de pago', 'slug' => 'editar-medio-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '429', 'nombre' => 'Actualizar medio de pago', 'slug' => 'actualizar-medio-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '430', 'nombre' => 'Borrar medio de pago', 'slug' => 'borrar-medio-pago', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
