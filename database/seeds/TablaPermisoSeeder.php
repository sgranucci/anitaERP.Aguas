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
            array('id' => '301', 'nombre' => 'Crear incoterms', 'slug' => 'crear-incoterms', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '302', 'nombre' => 'Listar incoterms', 'slug' => 'listar-incoterms', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '303', 'nombre' => 'Editar incoterms', 'slug' => 'editar-incoterms', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '304', 'nombre' => 'Actualizar incoterms', 'slug' => 'editar-incoterms', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '305', 'nombre' => 'Borrar incoterms', 'slug' => 'borrar-incoterms', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
