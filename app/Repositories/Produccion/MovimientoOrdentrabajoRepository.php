<?php

namespace App\Repositories\Produccion;

use App\Models\Produccion\MovimientoOrdentrabajo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Auth;

class MovimientoOrdentrabajoRepository implements MovimientoOrdentrabajoRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(MovimientoOrdentrabajo $movimientoordentrabajo)
    {
        $this->model = $movimientoordentrabajo;
    }

    public function estadoEnum()
    {
        return $this->model->estadoEnum();
    }

    public function all()
    {
        return $this->model->with('ordenestrabajo_tarea')->with('tareas')->with('operaciones')->with('empleados')->get();
    }

    public function create(array $data)
    {
        $movimientoordentrabajo = $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $movimientoordentrabajo = $this->model->findOrFail($id)->update($data);

		return $movimientoordentrabajo;
    }

    public function delete($id)
    {
    	$movimientoordentrabajo = MovimientoOrdenTrabajo::find($id);

        $movimientoordentrabajo = $this->model->destroy($id);

		return $movimientoordentrabajo;
    }

    public function find($id)
    {
        if (null == $movimientoordentrabajo = $this->model->with('ordenestrabajo')->with('tareas')->with('operaciones')->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $movimientoordentrabajo;
    }

    public function findOrFail($id)
    {
        if (null == $movimientoordentrabajo = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $movimientoordentrabajo;
    }

}
