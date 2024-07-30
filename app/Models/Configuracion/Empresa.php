<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;

class Empresa extends Model
{
    protected $fillable = ['nombre', 'domicilio', 'nroinscripcion', 'codigo'];
    protected $table = 'empresa';
    protected $tableAnita = 'emprmae';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = 'empm_empresa';

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'campos' => $this->keyFieldAnita, 'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = Empresa::all();
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
                empm_empresa,
				empm_nombre,
				empm_direccion,
				empm_localidad,
				empm_provincia,
				empm_cod_postal,
				empm_cuit,
				empm_ult_depura,
				empm_mes_inicio,
				empm_ejer_anio
            ' , 
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];
            Empresa::create([
                "id" => $key,
                "nombre" => $data->empm_nombre,
                "domicilio" => $data->empm_direccion,
                "nroinscripcion" => $data->empm_cuit,
				"codigo" => $data->empm_empresa
            ]);
        }
    }

	public function guardarAnita($request) {
        $apiAnita = new ApiAnita();

        $data = array( 'tabla' => $this->tableAnita, 
						'acc' => 'insert',
            			'campos' => ' empm_empresa, empm_nombre, empm_direccion, empm_localidad, empm_provincia, empm_cod_postal, empm_cuit, empm_ult_depura, empm_mes_inicio, empm_ejer_anio',
            			'valores' => " '".$request->codigo."', '".$request->nombre."', '".$request->domicilio."', '".' '."', '".' '."', '".'0'.", ".$request->nroinscripcion."', '".'0'."', ".'0'.", '".'0'."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request) {
        $apiAnita = new ApiAnita();

		$data = array( 'acc' => 'update', 
						'tabla' => $this->tableAnita, 
						'valores' => " empm_nombre = '".$request->nombre."', empm_direccion = '".$request->domicilio."', empm_cuit = '".$request->nroinscripcion."' ", 
						'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$request->codigo."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita,
					'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}
}
