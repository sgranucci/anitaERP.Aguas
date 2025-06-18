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
            array('id' => '545', 'nombre' => 'Ingresa movil', 'slug' => 'crea-movil', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '546', 'nombre' => 'Lista movil', 'slug' => 'lista-movil', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '547', 'nombre' => 'Edita movil', 'slug' => 'edita-movil', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '548', 'nombre' => 'Actualiza movil', 'slug' => 'actualiza-movil', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '549', 'nombre' => 'Borra movil', 'slug' => 'borra-movil', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
