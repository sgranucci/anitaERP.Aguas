<?php

namespace App\Repositories\Configuracion;

use App\Models\Configuracion\Impuesto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Auth;

class ImpuestoRepository implements ImpuestoRepositoryInterface
{
    protected $model;
    protected $tableAnita = 'impvar';
    protected $keyField = 'id';
    protected $keyFieldAnita = 'impv_codigo';

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Impuesto $empresa)
    {
        $this->model = $empresa;
    }

    public function all()
    {
        $hay_empresa = Impuesto::first();

        if (!$hay_empresa)
			self::sincronizarConAnita();

        return $this->model->orderBy('nombre','ASC')->get();
    }

    public function create(array $data)
    {
        $empresa = $this->model->create($data);
		//
		// Graba anita
		self::guardarAnita($data);
    }

    public function update(array $data, $id)
    {
        $empresa = $this->model->findOrFail($id)
            ->update($data);

        // Actualiza anita
		self::actualizarAnita($data, $data['codigo']);

		return $empresa;
    }

    public function delete($id)
    {
    	$empresa = $this->model->find($id);
		//
		// Elimina anita
		self::eliminarAnita($empresa->codigo);

        $empresa = $this->model->destroy($id);

		return $empresa;
    }

    public function find($id)
    {
        if (null == $empresa = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $empresa;
    }

    public function findPorId($id)
    {
        $empresa = $this->model->where('id', $id)->first();

        return $empresa;
    }

    public function findPorCodigo($codigo)
    {
        $empresa = $this->model->where('codigo', $codigo)->first();

        return $empresa;
    }

    public function findPorValor($valor)
    {
        $empresa = $this->model->where('valor', $valor)->first();

        return $empresa;
    }

    public function findOrFail($id)
    {
        if (null == $empresa = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $empresa;
    }

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 
                        'sistema' => 'shared',
                        'campos' => $this->keyFieldAnita, 
                        'tabla' => $this->tableAnita );
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
            'sistema' => 'shared',
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

	public function guardarAnita($request) {
        $apiAnita = new ApiAnita();

		$fechavigencia = $request['fechavigencia'];
		$fechavigencia = date('Ymd', strtotime($fechavigencia));

        $data = array( 'tabla' => $this->tableAnita, 
						'acc' => 'insert',
                        'sistema' => 'shared',
            			'campos' => ' impv_codigo, impv_desc, impv_tasa, impv_fecha',
            			'valores' => " '".$request['codigo']."', '".$request['nombre']."', '".$request['valor']."', '".$fechavigencia."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();

        $fechavigencia = $request['fechavigencia'];
		$fechavigencia = date('Ymd', strtotime($fechavigencia));

		$data = array( 'acc' => 'update', 
						'tabla' => $this->tableAnita, 
                        'sistema' => 'shared',
						'valores' => " impv_desc = '".$request['nombre']."', impv_tasa = '".$request['valor']."', impv_fecha = '".$fechavigencia."' ", 
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
