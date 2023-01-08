<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;

class Provincia extends Model
{
    protected $fillable = ['nombre', 'abreviatura', 'jurisdiccion', 'codigo', 'pais_id'];
    protected $table = 'provincia';
    protected $keyField = 'id';
    protected $keyFieldAnita = 'provi_provincia';

    public function paises()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 
					'campos' => $this->keyFieldAnita, 
					'orderBy' => $this->keyFieldAnita, 
					'tabla' => $this->table );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = Provincia::all();
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
            'acc' => 'list', 'tabla' => $this->table, 
            'campos' => '
                provi_provincia,
				provi_desc,
				provi_abrev,
				provi_jurisdiccion,
				provi_cod_externo
            ' , 
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];
            Provincia::create([
                "id" => $key,
                "nombre" => $data->provi_desc,
                "abreviatura" => $data->provi_abrev,
                "jurisdiccion" => $data->provi_jurisdiccion,
                "codigo" => $data->provi_cod_externo,
                "pais_id" => 1
            ]);
        }
    }

	public function guardarAnita($request, $id) {
        $apiAnita = new ApiAnita();

        $data = array( 'tabla' => $this->table, 
						'acc' => 'insert',
            			'campos' => ' provi_provincia, provi_desc, provi_abrev, provi_jurisdiccion, provi_cod_externo',
            			'valores' => " '".$id."', 
										'".$request->nombre."',  
										'".$request->abreviatura."',
										'".$request->jurisdiccion."',
										'".$request->codigo."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();
		$data = array( 'acc' => 'update', 
						'tabla' => $this->table, 
						'valores' => 
							" provi_desc = '".$request->nombre."',
							provi_abrev = '".$request->abreviatura."',
							provi_jurisdiccion = '".$request->jurisdiccion."',
                			provi_cod_externo =	'".$request->codigo."'",
						'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 
					'tabla' => $this->table,
					'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}
}
