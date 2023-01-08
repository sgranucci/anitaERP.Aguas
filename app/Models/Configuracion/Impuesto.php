<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;

class Impuesto extends Model
{
    protected $fillable = ['nombre', 'valor', 'fechavigencia'];
    protected $table = 'impuesto';
    protected $tableAnita = 'impvar';
    protected $keyField = 'id';
    protected $keyFieldAnita = 'impv_codigo';

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'campos' => $this->keyFieldAnita, 'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = Impuesto::all();
        $datosLocalArray = [];
        foreach ($datosLocal as $value) {
            $datosLocalArray[] = $value->{$this->keyField};
        }
        
		if ($dataAnita)
		{
        	foreach ($dataAnita as $value) {
            	if (!in_array($value->{$this->keyFieldAnita}, $datosLocalArray)) {
                	$this->traerRegistroDeAnita($value->{$this->keyFieldAnita});
            	}
        	}
		}
    }

    public function traerRegistroDeAnita($key){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'campos' => '
                impv_codigo,
				impv_desc,
				impv_tasa,
				impv_fecha
            ' , 
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		$fechavigencia = date('Y-m-d', strtotime($dataAnita[0]->impv_fecha));

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];
            Impuesto::create([
                "id" => $key,
                "nombre" => $data->impv_desc,
                "valor" => $data->impv_tasa,
				"fechavigencia" => $fechavigencia
            ]);
        }
    }

	public function guardarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		$fechavigencia = $request->fechavigencia;
		$fechavigencia = date('Ymd', strtotime($fechavigencia));

        $data = array( 'tabla' => $this->tableAnita, 
						'acc' => 'insert',
            			'campos' => ' impv_codigo, impv_desc, impv_tasa, impv_fecha',
            			'valores' => " '".$id."', '".$request->nombre."', '".$request->valor."', '".$fechavigencia."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		$fechavigencia = $request->fechavigencia;
		$fechavigencia = date('Ymd', strtotime($fechavigencia));

		$data = array( 'acc' => 'update', 
						'tabla' => $this->tableAnita, 
						'valores' => " impv_desc = '".$request->nombre."', impv_tasa = '".$request->valor."', impv_fecha = '".$fechavigencia."' ", 
						'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita,
					'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}
}
