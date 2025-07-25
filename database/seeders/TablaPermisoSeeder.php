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
            array('id' => '565', 'nombre' => 'Ingresa rendicion receptivo', 'slug' => 'crea-rendicion-receptivo', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '566', 'nombre' => 'Lista rendicion receptivo', 'slug' => 'lista-rendicion-receptivo', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '567', 'nombre' => 'Edita rendicion receptivo', 'slug' => 'edita-rendicion-receptivo', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '568', 'nombre' => 'Actualiza rendicion receptivo', 'slug' => 'actualiza-rendicion-receptivo', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '569', 'nombre' => 'Borra rendicion receptivo', 'slug' => 'borra-rendicion-receptivo', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
