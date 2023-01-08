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

    public function allQueryporEstado(array $campos, $estado)
    {
        return $this->model->select($campos)
                ->orderBy('nombre','ASC')
                ->where('estado',$estado)
                ->where('nombre','!=',' ')->get();
    }

    public function allQueryCargaPedido(array $campos)
    {
        return $this->model->select($campos)
                ->orderBy('nombre','ASC')
                ->where([['estado','0'],['nombre','!=',' ']])
                ->orWhere([['estado','!=','0'],['nombre','!=',' '],['tiposuspension_id','!=','1']])
                ->get();
    }

    public function traeClienteporCodigo($codigo)
    {
        return $this->model->select('id','codigo')->where('codigo',$codigo)->first();
    }

    public function traeClienteporId($id, $campos = null)
    {
	  	if ($campos)
        	return $this->model->with('condicionivas')->select($campos)->where('id',$id)->first();
		else
        	return $this->model->with('condicionivas')->where('id',$id)->first();
    }

    // Datos para informe maestro de clientes

    public function generaDatosRepCliente($desdecliente_id, $hastacliente_id, $estado, $tiposuspensioncliente_id)
    {
        $data = $this->model->select('cliente.*')
            ->with('localidades')
            ->with('provincias')
            ->with('tipossuspensioncliente')
            ->orderBy('nombre','ASC');

        switch($estado)
        {
            case 'TODOS':
                $data = $data
                    ->where([['nombre','!=',' ']])
                    ->get();
                break;
            case 'ACTIVOS':
                $data = $data
                    ->where([['estado','0'],['nombre','!=',' ']])
                    ->get();
                break;
            case 'SUSPENDIDOS':
                if ($tiposuspensioncliente_id != 0)
                    $data = $data
                        ->where([['estado','!=','0'],['nombre','!=',' '],['tiposuspension_id',$tiposuspensioncliente_id]])
                        ->get();
                else
                    $data = $data
                        ->where([['estado','!=','0'],['nombre','!=',' ']])
                        ->get();
                break;
        }
        return $data;
    }

}

