<?php

namespace App\Repositories\Produccion;

use App\Models\Produccion\Empleado;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Auth;

class EmpleadoRepository implements EmpleadoRepositoryInterface
{
    protected $model;
    protected $tableAnita = 'empleado';
    protected $keyField = 'id';
    protected $keyFieldAnita = 'emp_legajo';

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Empleado $empleado)
    {
        $this->model = $empleado;
    }

    public function all()
    {
        $hay_empleados = Empleado::first();

		if (!$hay_empleados)
			self::sincronizarConAnita();

        return $this->model->get();
    }

    public function create(array $data)
    {
        $empleado = $this->model->create($data);
		//
		// Graba anita
		self::guardarAnita($data);
    }

    public function update(array $data, $id)
    {
        $empleado = $this->model->findOrFail($id)
            ->update($data);
		//
		// Actualiza anita
		self::actualizarAnita($data, $id);

		return $empleado;
    }

    public function delete($id)
    {
    	$empleado = Empleado::find($id);
		//
		// Elimina anita
		self::eliminarAnita($empleado->codigo);

        $empleado = $this->model->destroy($id);

		return $empleado;
    }

    public function find($id)
    {
        if (null == $empleado = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $empleado;
    }

    public function findOrFail($id)
    {
        if (null == $empleado = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $empleado;
    }

    public function sincronizarConAnita(){
		ini_set('max_execution_time', '300');

        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 
						'campos' => "$this->keyFieldAnita as $this->keyField, $this->keyFieldAnita", 
						'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = $this->model->all();
        $datosLocalArray = [];
        foreach ($datosLocal as $value) {
            $datosLocalArray[] = $value->{$this->keyField};
        }

        foreach ($dataAnita as $value) {
            if (!in_array($value->{$this->keyField}, $datosLocalArray)) {
                $this->traerRegistroDeAnita($value->{$this->keyFieldAnita});
            }
        }
    }

    public function traerRegistroDeAnita($key){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'campos' => '
			emp_nombre
			',
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		$usuario_id = Auth::user()->id;

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];

			$arr_campos = [
				"nombre" => $data->emp_nombre,
            	];
	
        	$empleado = $this->model->create($arr_campos);
        }
    }

	public function guardarAnita($request) {
        $apiAnita = new ApiAnita();

        $data = array( 'tabla' => $this->tableAnita, 'acc' => 'insert',
            'campos' => ' 
				emp_empleado,
    			emp_nombre
				',
            'valores' => " 
				'".$request['id']."', 
				'".$request['nombre']."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		$data = array( 'acc' => 'update', 'tabla' => $this->tableAnita, 
				'valores' => " 
                emp_nombre 	                = '".$request['nombre']."' "
					,
				'whereArmado' => " WHERE emp_empleado = '".$id."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita, 
				'whereArmado' => " WHERE emp_empleado = '".$id."' " );
        $apiAnita->apiCall($data);
	}

}
