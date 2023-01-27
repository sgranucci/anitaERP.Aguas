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
            array('id' => '306', 'nombre' => 'Crear formas de pago', 'slug' => 'crear-formas-de-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '307', 'nombre' => 'Listar formas de pago', 'slug' => 'listar-formas-de-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '308', 'nombre' => 'Editar formas de pago', 'slug' => 'editar-formas-de-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '309', 'nombre' => 'Actualizar formas de pago', 'slug' => 'editar-formas-de-pago', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '310', 'nombre' => 'Borrar formas de pago', 'slug' => 'borrar-formas-de-pago', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
