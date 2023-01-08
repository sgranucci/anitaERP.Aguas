<?php

namespace App\Repositories\Ventas;

use App\Models\Ventas\Cliente_Cuentacorriente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Carbon\Carbon;
use Auth;

class Cliente_CuentacorrienteRepository implements Cliente_CuentacorrienteRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Cliente_Cuentacorriente $cliente_cuentacorriente)
    {
        $this->model = $cliente_cuentacorriente;
    }

    public function create(array $data)
    {
        $cliente_cuentacorriente = $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $cliente_cuentacorriente = $this->model->findOrFail($id)->update($data);

		return $cliente_cuentacorriente;
    }

    public function delete($id)
    {
    	$cliente_cuentacorriente = $this->model->destroy($id);

		return $cliente_cuentacorriente;
    }

    public function find($id)
    {
        if (null == $cliente_cuentacorriente = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cliente_cuentacorriente;
    }

    public function findOrFail($id)
    {
        if (null == $cliente_cuentacorriente = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cliente_cuentacorriente;
    }
}

