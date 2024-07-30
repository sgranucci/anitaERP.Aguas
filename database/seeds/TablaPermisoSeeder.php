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
            array('id' => '351', 'nombre' => 'Ingresar talonario de rendicion', 'slug' => 'crear-talonario-de-rendicion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '352', 'nombre' => 'Listar talonario de rendicion', 'slug' => 'listar-talonario-de-rendicion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '353', 'nombre' => 'Editar talonario de rendicion', 'slug' => 'editar-talonario-de-rendicion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '354', 'nombre' => 'Actualizar talonario de rendicion', 'slug' => 'actualizar-talonario-de-rendicion', 'created_at' => $now, 'updated_at' => $now),
            array('id' => '355', 'nombre' => 'Borrar talonario de rendicion', 'slug' => 'borrar-talonario-de-rendicion', 'created_at' => $now, 'updated_at' => $now),

        ];
        DB::table('permiso')->insert($permisos);
    }
}
