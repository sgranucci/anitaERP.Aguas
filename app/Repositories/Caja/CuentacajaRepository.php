<?php

namespace App\Repositories\Caja;

use App\Models\Caja\Cuentacaja;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CuentacajaRepository implements CuentacajaRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Cuentacaja $cuentacaja)
    {
        $this->model = $cuentacaja;
    }

    public function all()
    {
        return $this->model->get();
    }

    public function create(array $data)
    {
        $cuentacaja = $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $cuentacaja = $this->model->findOrFail($id)->update($data);

		return $cuentacaja;
    }

    public function delete($id)
    {
    	$cuentacaja = $this->model->find($id);

        $cuentacaja = $this->model->destroy($id);

		return $cuentacaja;
    }

    public function find($id)
    {
        if (null == $cuentacaja = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cuentacaja;
    }

    public function findOrFail($id)
    {
        if (null == $cuentacaja = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cuentacaja;
    }
}
