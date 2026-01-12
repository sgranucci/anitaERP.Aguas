<?php

namespace App\Repositories\Receptivo;

use App\Models\Receptivo\Guia_Cuentacorriente;
use App\Services\Configuracion\CotizacionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Auth;

class Guia_CuentacorrienteRepository implements Guia_CuentacorrienteRepositoryInterface
{
    protected $model;
    private $cotizacionService;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Guia_Cuentacorriente $guia_cuentacorriente,
                                CotizacionService $cotizacionservice)
    {
        $this->model = $guia_cuentacorriente;
        $this->cotizacionService = $cotizacionservice;
    }

    public function create(array $data)
    {
        return self::guardarCuentacorriente($data, 'create');
    }

    public function updateUnique(array $data, $id)
    {
        $guia_cuentacorriente = $this->model->findOrFail($id)->update($data);

		return $guia_cuentacorriente;
    }

    public function update(array $data, $id, $campo)
    {
        return self::guardarCuentacorriente($data, 'update', $id, $campo);        
    }

    public function delete($id)
    {
    	$guia_cuentacorriente = $this->model->destroy($id);

		return $guia_cuentacorriente;
    }

    public function deletePorCajaMovimientoId($caja_movimiento_id)
    {
        return $this->model->where('caja_movimiento_id', $caja_movimiento_id)->delete();
    }

    public function find($id)
    {
        if (null == $guia_cuentacorriente = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $guia_cuentacorriente;
    }

    public function findPorRendicionreceptivoId($rendicionreceptivo_id)
    {
        $guia_cuentacorriente = $this->model->where('rendicionreceptivo_id', $rendicionreceptivo_id)->get();

        return $guia_cuentacorriente;
    }

    public function findOrFail($id)
    {
        if (null == $guia_cuentacorriente = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $guia_cuentacorriente;
    }

	private function guardarCuentacorriente($data, $funcion, $id = null, $campo = null)
	{
		if ($funcion == 'update')
		{
			// Trae todos los id
            $guia_cuentacorriente = $this->model->where($campo, $id)->get()->pluck('id')->toArray();
			$q_guia_cuentacorriente = count($guia_cuentacorriente);
		}

		// Graba cuentas contables
		if (isset($data['montorendiciones']))
		{
			$montorendiciones = $data['montorendiciones'];
            $moneda_ids = $data['monedarendicion_ids'];

			if ($funcion == 'update')
			{
				$_id = $guia_cuentacorriente;

				// Borra los que sobran
				if ($q_guia_cuentacorriente > count($montorendiciones))
				{
					for ($d = count($montorendiciones); $d < $q_guia_cuentacorriente; $d++)
						$this->model->find($_id[$d])->delete();
				}

				// Actualiza los que ya existian
				for ($i = 0; $i < $q_guia_cuentacorriente && $i < count($montorendiciones); $i++)
				{
					if ($i < count($montorendiciones) && $montorendiciones[$i] != null)
					{
						$guia_cuentacorriente = $this->model->findOrFail($_id[$i])->update([
                                    "fecha" => $data['fecha'],
                                    "guia_id" => $data['guia_id'],
									"rendicionreceptivo_id" => $data['rendicionreceptivo_id'],
									"caja_movimiento_id" => $data['caja_movimiento_id'],
                                    "monto" => $montorendiciones[$i],
                                    "moneda_id" => $moneda_ids[$i],
                                    "cotizacion" => $data['cotizacionrendiciones'][$i]
									]);
					}
				}
				if ($q_guia_cuentacorriente > count($montorendiciones))
					$i = $d; 
			}
			else
				$i = 0;

			for ($i_movimiento = $i; $i_movimiento < count($montorendiciones); $i_movimiento++)
			{
				if ($montorendiciones[$i_movimiento] != null) 
				{
					$guia_cuentacorriente = $this->model->create([
                            "fecha" => $data['fecha'],
                            "guia_id" => $data['guia_id'],
                            "rendicionreceptivo_id" => $data['rendicionreceptivo_id'],
                            "caja_movimiento_id" => $data['caja_movimiento_id'],
                            "monto" => $montorendiciones[$i_movimiento],
                            "moneda_id" => $moneda_ids[$i_movimiento],
                            "cotizacion" => $data['cotizacionrendiciones'][$i_movimiento]
						]);
				}
			}
		}
		else
		{
            if ($data['rendicionreceptivo_id'] > 0)
			    $guia_cuentacorriente = $this->model->where('rendicionreceptivo_id', $data['rendicionreceptivo_id'])->delete();
            else
                $guia_cuentacorriente = $this->model->where('caja_movimiento_id', $data['caja_movimiento_id'])->delete();
		}

		return $guia_cuentacorriente;
	}    

    public function listarCuentaCorriente($busqueda, $guia_id, $flPaginar)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $cuentacorriente = $this->model->select('guia_cuentacorriente.fecha as fecha', 
                                                'rendicionreceptivo.ordenservicio_id as rendicion_ordenservicio_id',
                                                'rendicionreceptivo.id as rendicionreceptivo_id',
                                                'caja_movimiento.ordenservicio_id as caja_movimiento_ordenservicio_id',
                                                'caja_movimiento.id as caja_movimiento_id',
                                                'rendicionreceptivo.numerotalonario as numerorendicion',
                                                'tipotransaccion_caja.abreviatura as abreviaturatipotransaccion',
                                                'caja_movimiento.numerotransaccion as numerotransaccion',
                                                'guia_cuentacorriente.moneda_id as moneda_id',
                                                'moneda.abreviatura as abreviaturamoneda',
                                                'guia_cuentacorriente.monto as monto'
                                                )
                                        ->leftjoin('moneda', 'moneda.id', '=', 'guia_cuentacorriente.moneda_id')
                                        ->leftjoin('rendicionreceptivo', 'rendicionreceptivo.id', '=', 'guia_cuentacorriente.rendicionreceptivo_id')
								        ->leftjoin('caja_movimiento', 'caja_movimiento.id', '=', 'guia_cuentacorriente.caja_movimiento_id')
                                        ->leftjoin('tipotransaccion_caja', 'tipotransaccion_caja.id', '=', 'caja_movimiento.tipotransaccion_caja_id')
                                        ->where('guia_cuentacorriente.guia_id', $guia_id)
                                        ->where(function ($query) use ($busqueda) {
                                            $query->orWhere('moneda.abreviatura', 'like', "%{$busqueda}%");
                                            $query->orwhere('rendicionreceptivo.ordenservicio_id', 'like', "%{$busqueda}%");
                                            $query->orwhere('caja_movimiento.ordenservicio_id', 'like', "%{$busqueda}%");
                                            $query->orwhere('rendicionreceptivo.numerotalonario', 'like', "%{$busqueda}%");
                                            $query->orwhere('monto', 'like', "%{$busqueda}%");
                                        })
                                        ->orderBy('fecha', 'asc');


        if ($flPaginar)
            $cuentacorriente = $cuentacorriente->paginate(10);								
        else
            $cuentacorriente = $cuentacorriente->get();
        		
        return $cuentacorriente;
    }    
}

