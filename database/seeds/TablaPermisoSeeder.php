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
            array('id' => '181', 'nombre' => 'Crear localidades', 'slug' => 'crear-localidades', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '182', 'nombre' => 'Listar localidades', 'slug' => 'listar-localidades', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '183', 'nombre' => 'Editar localidades', 'slug' => 'editar-localidades', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '184', 'nombre' => 'Actualizar localidades', 'slug' => 'editar-localidades', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '185', 'nombre' => 'Borrar localidades', 'slug' => 'borrar-localidades', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
