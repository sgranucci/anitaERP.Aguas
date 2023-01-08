<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;

class Vendedor extends Model
{
    protected $fillable = ['nombre', 'comisionventa', 'comisioncobranza'];
    protected $table = 'vendedor';
    protected $keyField = 'vend_codigo';

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'campos' => $this->keyField, 
						'tabla' => $this->table, 
						'orderBy' => 'vend_codigo' );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = Vendedor::all();
        $datosLocalArray = [];
        foreach ($datosLocal as $value) {
            $datosLocalArray[] = $value->{$this->keyField};
        }
        
        foreach ($dataAnita as $value) {
            if (!in_array($value->{$this->keyField}, $datosLocalArray)) {
                $this->traerRegistroDeAnita($value->{$this->keyField});
            }
        }
    }

    public function traerRegistroDeAnita($key){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->table, 
            'campos' => '
                vend_codigo,
				vend_nombre,
				vend_comision_vta,
				vend_comision_cob
            ' , 
            'whereArmado' => " WHERE ".$this->keyField." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];
            Vendedor::create([
                "id" => $key,
                "nombre" => $data->vend_nombre,
                "comisionventa" => $data->vend_comision_vta,
                "comisioncobranza" => $data->vend_comision_cob
            ]);
        }
    }

	public function guardarAnita($request, $id) {
        $apiAnita = new ApiAnita();

        $data = array( 'tabla' => 'vendedor', 'acc' => 'insert',
            'campos' => ' vend_codigo, vend_nombre, vend_comision_vta, vend_aplicacion, vend_empresa, vend_letra, vend_comision_cob, vend_mercaderia ',
            'valores' => " '".$id."', 
						   '".$request->nombre."',
						   '".$request->comisionventa."',
						   'B',
						   '0',
						   '0',
            			   '".$request->comisioncobranza."',
						   ' '"
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();
		$data = array( 'acc' => 'update', 'tabla' => 'vendedor', 
					'valores' => " 
								vend_nombre = '".  $request->nombre."',
								vend_comision_vta = '".  $request->comisionventa."', 
								vend_comision_cob = '".  $request->comisioncobranza."' ", 
					'whereArmado' => " WHERE vend_codigo = '".$id."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 
						'tabla' => 'vendedor', 
						'whereArmado' => " WHERE vend_codigo = '".$id."' " );
        $apiAnita->apiCall($data);
	}
}
