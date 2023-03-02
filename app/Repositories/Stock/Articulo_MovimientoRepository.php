<?php

namespace App\Repositories\Stock;

use App\Models\Stock\Articulo_Movimiento;
use App\Models\Stock\Articulo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use App\ApiAnita;
use Auth;

class Articulo_MovimientoRepository implements Articulo_MovimientoRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Articulo_Movimiento $articulo_movimiento)
    {
        $this->model = $articulo_movimiento;
    }

    public function all()
    {
        $ret = $this->model->get();

        return $ret;
    }

    public function find($id)
    {
        if (null == $articulo_movimiento = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $articulo_movimiento;
    }

    public function findPorArticuloCombinacion($articulo_id, $combinacion_id)
    {
        return $this->model->where('articulo_id', $articulo_id)->where('combinacion_id', $combinacion_id)->get();
    }

    public function findPorPedidoCombinacionId($pedido_combinacion_id)
    {
        return $this->model->where('pedido_combinacion_id', $pedido_combinacion_id)->first();
    }

    public function updatePorPedidoCombinacionId($pedido_combinacion_id, $data)
    {
        return $this->model->where('pedido_combinacion_id', $pedido_combinacion_id)->update($data);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->findOrFail($id)->update($data);
    }

    public function delete($id)
    {
    	$articulo_movimiento = $this->model->destroy($id);

		return $articulo_movimiento;
    }

    public function deletePorMovimientoStockId($movimientostock_id)
    {
        return $this->model->where('movimientostock_id', $movimientostock_id)->delete();
    }

}
