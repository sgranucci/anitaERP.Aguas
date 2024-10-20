<?php

namespace App\Queries\Compras;

use App\Models\Compras\Proveedor;

class ProveedorQuery implements ProveedorQueryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Proveedor $proveedor)
    {
        $this->model = $proveedor;
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

    public function allQueryOrdenado(array $campos, $orden)
    {
        return $this->model->select($campos)->orderBy($orden)->where('nombre', '!=', ' ')->get();
    }

    public function allQueryporEstado(array $campos, $estado, $orden = null)
    {
        $proveedor = $this->model->select($campos)
                ->orderBy('nombre','ASC')
                ->where('estado',$estado)
                ->where('nombre','!=',' ');

        if ($orden)
            $proveedor = $proveedor->orderBy($orden);

        $proveedor = $proveedor->get();

        return($proveedor);
    }

    public function traeProveedorporCodigo($codigo)
    {
        return $this->model->select('id','codigo')->where('codigo',$codigo)->first();
    }

    public function traeProveedorporId($id, $campos = null)
    {
	  	if ($campos)
        	return $this->model->with('condicionivas')->select($campos)->where('id',$id)->first();
		else
        	return $this->model->with('condicionivas')->where('id',$id)->with('paises')->with('condicionventas')->first();
    }

    // Datos para informe maestro de proveedores

    public function generaDatosRepProveedor($desdeproveedor_id, $hastaproveedor_id, $estado, $tiposuspensionproveedor_id,
                                        $desdevendedor_id, $hastavendedor_id)
    {
        $data = $this->model->select('proveedor.*')
            ->with('localidades')
            ->with('provincias')
            ->with('tipossuspensionproveedor')
            ->orderBy('nombre','ASC');

        switch($estado)
        {
            case 'TODOS':
                $data = $data
                    ->where([['nombre','!=',' ']]);
                break;
            case 'ACTIVOS':
                $data = $data
                    ->where([['estado','0'],['nombre','!=',' ']]);
                break;
            case 'SUSPENDIDOS':
                if ($tiposuspensionproveedor_id != 0)
                    $data = $data
                        ->where([['estado','!=','0'],['nombre','!=',' '],['tiposuspension_id',$tiposuspensionproveedor_id]]);
                else
                    $data = $data
                        ->where([['estado','!=','0'],['nombre','!=',' ']]);
                break;
        }
        // Filtra por proveedor 
        if ($hastaproveedor_id != 99999999 || $desdeproveedor_id != 0)
            $data = $data->where('proveedor.id', '>=', $desdeproveedor_id)
                        ->where('proveedor.id', '<=', $hastaproveedor_id);

        // Filtra por vendedor
        if ($hastavendedor_id != 99999999 || $desdevendedor_id != 0)
            $data = $data->where('proveedor.vendedor_id', '>=', $desdevendedor_id)
                        ->where('proveedor.vendedor_id', '<=', $hastavendedor_id);

        $data = $data->get();
        return $data;
    }

}

