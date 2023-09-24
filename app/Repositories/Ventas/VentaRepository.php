<?php

namespace App\Repositories\Ventas;

use App\Models\Ventas\Venta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Auth;
use App\ApiAnita;

class VentaRepository implements VentaRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Venta $venta)
    {
        $this->model = $venta;
    }

    public function all()
    {
        return $this->model->get();
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
    	return $this->model->destroy($id);
    }

    public function find($id)
    {
        if (null == $venta = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $venta;
    }

    public function findOrFail($id)
    {
        if (null == $venta = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $venta;
    }

    public function traeUltimoNumeroRemito($tipo, $letra, $sucursal)
    {
        // Lee numerador desde anita
		$apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 
			'tabla' => 'compemis', 
            'campos' => '
                compe_numero
			' , 
            'whereArmado' => " WHERE compe_tipo='".$tipo."' and compe_letra='".$letra."' 
                                    and compe_sucursal='".$sucursal."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));
        
        if (count($dataAnita) > 0)
        {
            $claveNumero = $dataAnita[0]->compe_numero;

            $apiAnita = new ApiAnita();
            $data = array( 
                'acc' => 'list', 
                'tabla' => 'numerador', 
                'campos' => '
                    num_ult_numero
                ' , 
                'whereArmado' => " WHERE num_clave='".$claveNumero."' " 
            );
            $dataAnita = json_decode($apiAnita->apiCall($data));

            $nro = $dataAnita[0]->num_ult_numero + 1;
        }
        
        //$venta = $this->model->where('puntoventaremito_id', $puntoventaremito_id)->max('numeroremito');
		//$nro = 0;
		//if ($venta)
		//	$nro = $venta;
		//$nro = $nro + 1;
        if (!isset($nro))
            return 'error';
        
        return $nro;
    }

    public function numeraAnita($tipo, $letra, $sucursal)
    {
        // Lee numerador desde anita
		$apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 
			'tabla' => 'compemis', 
            'campos' => '
                compe_numero
			' , 
            'whereArmado' => " WHERE compe_tipo='".$tipo."' and compe_letra='".$letra."' 
                                    and compe_sucursal='".$sucursal."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if (count($dataAnita) > 0)
        {
            $claveNumero = $dataAnita[0]->compe_numero;

            $apiAnita = new ApiAnita();
            $data = array( 
                'acc' => 'list', 
                'tabla' => 'numerador', 
                'campos' => '
                    num_ult_numero
                ' , 
                'whereArmado' => " WHERE num_clave='".$claveNumero."' " 
            );
            $dataAnita = json_decode($apiAnita->apiCall($data));

            $numero = $dataAnita[0]->num_ult_numero + 1;

            $apiAnita = new ApiAnita();
            $data = array( 'acc' => 'update', 
                    'tabla' => 'numerador',
                    'valores' => "num_ult_numero = '".$numero."' ",
                    'whereArmado' => " WHERE num_clave = '".$claveNumero."' " 
                    );
            $numerador = $apiAnita->apiCall($data);

            if (strpos($numerador, 'Error') !== false)
                return 'Error al actualizar numerador';
        }
        else
            return 'Error no tiene numerador';

        return $numero;
    }

    public function traeUltimoComprobanteVenta($tipotransaccion_id, $puntoventa_id)
    {
        $venta = $this->model->select('numerocomprobante')
                                ->where('tipotransaccion_id', $tipotransaccion_id)
                                ->where('puntoventa_id', $puntoventa_id)
                                ->orderBy('numerocomprobante','desc')->first();

        return $venta;
    }
}
