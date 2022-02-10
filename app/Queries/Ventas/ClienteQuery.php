<?php

namespace App\Queries\Ventas;

use App\Models\Ventas\Cliente;

class ClienteQuery implements ClienteQueryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Cliente $cliente)
    {
        $this->model = $cliente;
    }

    public function first()
    {
        return $this->model->first();
    }

    public function all()
    {
        return $this->model->get();
    }

    public function allQuery(array $campos)
    {
        return $this->model->select($campos)->get();
    }

    public function traeClienteporCodigo($codigo)
    {
        return $this->model->select('id','codigo')->where('codigo',$codigo)->first();
    }

    public function traeClienteporId($id)
    {
        return $this->model->with('condicionivas')->where('id',$id)->first();
    }
}

