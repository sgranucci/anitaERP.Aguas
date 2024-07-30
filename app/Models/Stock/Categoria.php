<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;
use App\Models\Stock\Tipoarticulo;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'codigo', 'copiaot', 'tipoarticulo_id'];
    protected $table = 'categoria';
    protected $tableAnita = 'stkagr';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = 'stka_agrupacion';

    public function tipoarticulo()
    {
        return $this->belongsTo(Tipoarticulo::class, 'tipoarticulo_id');
    }

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'campos' => $this->keyFieldAnita, 'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = Categoria::all();
        $datosLocalArray = [];
        foreach ($datosLocal as $value) {
            $datosLocalArray[] = $value->{$this->keyField};
        }
        
        foreach ($dataAnita as $value) {
            if (!in_array(ltrim($value->{$this->keyFieldAnita}, '0'), $datosLocalArray)) {
                $this->traerRegistroDeAnita($value->{$this->keyFieldAnita});
            }
        }
    }

    public function traerRegistroDeAnita($key){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'campos' => '
                stka_agrupacion,
				stka_desc,
				stka_copiaot,
				stka_tipo_art,
				stka_id
            ' , 
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];

			// Traigo id del tipo de articulo 
			if ($data->stka_tipo_art == 'B')
				$tipoarticulo_id = 2;
			else
				$tipoarticulo_id = 1;

			$codigo = ltrim($data->stka_agrupacion, '0');

            Categoria::create([
                "id" => $data->stka_id,
                "nombre" => $data->stka_desc,
                "codigo" => $codigo,
				"copiaot" => $data->stka_copiaot,
				"tipoarticulo_id" => $tipoarticulo_id
            ]);
        }
    }

	public function guardarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		// Traigo id del tipo de articulo 
		if ($request->tipoarticulo_id == 2)
			$tipoarticulo = 'B';
		else
			$tipoarticulo = 'Z';

		$codigo = str_pad($request->codigo, 4, "0", STR_PAD_LEFT);

        $data = array( 'tabla' => $this->tableAnita, 'acc' => 'insert',
            'campos' => ' stka_agrupacion, stka_desc, stka_copiaot, stka_tipo_art, stka_id',
            'valores' => " '".$codigo."', '".$request->nombre."', '".$request->copiaot."', '".$tipoarticulo."', '".$id."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		// Traigo id del tipo de articulo 
		if ($request->tipoarticulo_id == 2)
			$tipoarticulo = 'B';
		else
			$tipoarticulo = 'Z';

		$codigo = str_pad($request->codigo, 4, "0", STR_PAD_LEFT);

        $data = array( 'tabla' => $this->tableAnita, 'acc' => 'update',
					'valores' => " stka_desc = '".  $request->nombre."' , stka_copiaot = '".$request->copiaot."' , stka_tipo_art = '".$tipoarticulo."' , stka_id = '".$request->id."'", 
					'whereArmado' => " WHERE stka_agrupacion = '".$codigo."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();

        $data = array( 'acc' => 'delete', 
			'tabla' => $this->tableAnita, 
			'whereArmado' => " WHERE stka_id = '".$id."' " );
        $apiAnita->apiCall($data);
	}
}
