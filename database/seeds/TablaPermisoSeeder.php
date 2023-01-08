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
            array('id' => '296', 'nombre' => 'Crear lotes', 'slug' => 'crear-lotes', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '297', 'nombre' => 'Listar lotes', 'slug' => 'listar-lotes', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '298', 'nombre' => 'Editar lotes', 'slug' => 'editar-lotes', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '299', 'nombre' => 'Actualizar lotes', 'slug' => 'editar-lotes', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '300', 'nombre' => 'Borrar lotes', 'slug' => 'borrar-lotes', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
