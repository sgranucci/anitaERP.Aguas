<?php

namespace App\Queries\Ventas;

use App\Models\Ventas\Pedido;

class PedidoQuery implements PedidoQueryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Pedido $pedido)
    {
        $this->model = $pedido;
    }

    public function allPendiente($cliente_id = null)
    {
		if ($cliente_id)
			$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')->with('pedido_combinaciones')->where('estado','0')->where('cliente_id',$cliente_id)->get();
		else
			$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')->with('pedido_combinaciones')->where('estado','0')->get();

		return $mod;
    }

    public function leePedidoporCodigo($codigo)
    {
        return $this->model->select('id')->where('codigo' , $codigo)->first();
    }

    public function leePedidoporId($id)
    {
		$mod = $this->model->with('clientes:id,nombre')->with('mventas:id,nombre')->with('transportes:id,nombre')->with('pedido_combinaciones')->where('estado','0')->where('id',$id)->get();

		return $mod;
    }

    public function first()
    {
        return $this->model->first();
	}

    public function all()
    {
        return $this->model->with('pedido_combinaciones')->get();
    }

}
