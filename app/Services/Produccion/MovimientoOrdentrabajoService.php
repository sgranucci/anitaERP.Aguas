<?php
namespace App\Services\Produccion;

use App\Repositories\Stock\Articulo_CostoRepositoryInterface;
use App\Repositories\Ventas\OrdentrabajoRepositoryInterface;
use App\Repositories\Ventas\Ordentrabajo_Combinacion_TalleRepositoryInterface;
use App\Repositories\Ventas\Ordentrabajo_TareaRepositoryInterface;
use App\Repositories\Produccion\MovimientoOrdentrabajoRepositoryInterface;
use App\Repositories\Produccion\OperacionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App;
use Auth;
use DB;

class MovimientoOrdentrabajoService 
{
	protected $ordentrabajo_tareaRepository;
	protected $ordentrabajo_combinacion_talleRepository;
	protected $ordentrabajoRepository;
	protected $operacionRepository;
	protected $movimientoordentrabajoRepository;
	protected $articulo_costoRepository;

    public function __construct(
								OrdentrabajoRepositoryInterface $ordentrabajorepository,
								Ordentrabajo_Combinacion_TalleRepositoryInterface $ordentrabajocombinaciontallerepository,
								Ordentrabajo_TareaRepositoryInterface $ordentrabajotarearepository,
								MovimientoOrdentrabajoRepositoryInterface $movimientoordentrabajorepository,
								Articulo_CostoRepositoryInterface $articulo_costorepository,
								OperacionRepositoryInterface $operacionrepository
								)
    {
        $this->ordentrabajoRepository = $ordentrabajorepository;
        $this->ordentrabajo_combinacion_talleRepository = $ordentrabajocombinaciontallerepository;
        $this->ordentrabajo_tareaRepository = $ordentrabajotarearepository;
        $this->movimientoordentrabajoRepository = $movimientoordentrabajorepository;
		$this->articulo_costoRepository = $articulo_costorepository;
        $this->operacionRepository = $operacionrepository;
    }

	public function estadoEnum()
	{
		return $this->movimientoordentrabajoRepository->estadoEnum(); 
	}

	public function leeMovimientoOrdenTrabajo($id = null)
	{
		if ($id)
      		$movimientoOrdentrabajo = $this->movimientoordentrabajoRepository->find($id);
		else
      		$movimientoOrdentrabajo = $this->movimientoordentrabajoRepository->all();

		return $movimientoOrdentrabajo;
	}

	public function guardaMovimientoOrdenTrabajo($data, $funcion, $id = null)
	{
		$usuario_id = Auth::user()->id;
		$tipooperacionEnum = $this->operacionRepository->tipooperacionEnum(); 
		$estadoEnum = self::estadoEnum(); 

		$codigos_ot = explode(',', $data['ordenestrabajo']);

		// Recorre cada id de ordenes de trabajo
		for ($i = 0; $i < count($codigos_ot); $i++)
		{
			// Lee Orden de trabajo
			try
			{
				$ordentrabajo = $this->ordentrabajoRepository->findPorCodigo($codigos_ot[$i]);
			} catch (\Exception $e) 
			{
				return $e->getMessage();
			}

			if ($ordentrabajo)
			{
				// Busca la tarea si existe
				$ordentrabajo_tarea = $this->ordentrabajo_tareaRepository->findPorOrdentrabajoId($ordentrabajo->id);

				// Lee la cantidad y articulo de la orden de trabajo
				$ordentrabajo_combinacion_talle = $this->ordentrabajo_combinacion_talleRepository->findPorOrdenTrabajoId($ordentrabajo->id);

				$sku = '';
				$articulo_id = 0;
				$cantidad = 0;
				if (count($ordentrabajo_combinacion_talle) > 0)
				{
					$sku = $ordentrabajo_combinacion_talle[0]->pedido_combinacion_talles->pedidos_combinacion->articulos->sku;
					$articulo_id = $ordentrabajo_combinacion_talle[0]->pedido_combinacion_talles->pedidos_combinacion->articulos->articulo_id;
					$pedido_combinacion_id = $ordentrabajo_combinacion_talle[0]->pedido_combinacion_talles->pedidos_combinacion->id;

					foreach ($ordentrabajo_combinacion_talle as $item)
						$cantidad += $item->pedido_combinacion_talles->cantidad;
				}

				$operacion = $this->operacionRepository->find($data['operacion_id']);

				// Busca el costo
				$articulo_costo = $this->articulo_costoRepository->findPorArticuloTarea($articulo_id, $data['tarea_id']);
				$costo = 0;
				if (count($articulo_costo) > 0)
					$costo = $articulo_costo->costo;

				DB::beginTransaction();
				try 
				{
					if (!$operacion)
						throw new ModelNotFoundException("No existe operacion");

					if (count($ordentrabajo_tarea) > 0 && $ordentrabajo_tarea->contains('tarea_id',
						config('consprod.TAREA_TERMINADA')))
						throw new ModelNotFoundException("No puede grabar con OT ya terminada");

					// Filtra tarea_id
					$ordentrabajo_tarea_filtrada = $this->ordentrabajo_tareaRepository
												->findPorOrdentrabajoId($ordentrabajo->id, $data['tarea_id']);
					
					if ($funcion == 'create')
					{
						$accion = '';
						if ($tipooperacionEnum[$operacion->tipooperacion] == 'Inicio')
						{
							if (count($ordentrabajo_tarea_filtrada) == 0)
								// Crea la tarea
								$accion = 'create';
							else
								throw new ModelNotFoundException("La tarea ya existe");
						}
						
						if ($tipooperacionEnum[$operacion->tipooperacion] == 'Fin')
						{
							if (count($ordentrabajo_tarea_filtrada) == 0)
								throw new ModelNotFoundException("La tarea no fue iniciada");
							
							// Actualiza la tarea
							$accion = 'update';
						}

						if ($accion != '')
						{
							$dataTarea = array(
								'ordentrabajo_id' => $ordentrabajo->id,
								'tarea_id' => $data['tarea_id'],
								'operacion_id' => $data['operacion_id'],
								'empleado_id' => $data['empleado_id'],
								'pedido_combinacion_id' => $pedido_combinacion_id,
								'fecha' => $data['fecha'],
								'estado' => $estadoEnum['A'],
								'usuario_id' => $usuario_id,
								// Campos para Anita
								'nro_orden' => $codigos_ot[$i],
								'codigo' => $codigos_ot[$i],
								'articulo' => str_pad($sku, 13, "0", STR_PAD_LEFT),
								'cantidad' => $cantidad,
								'costo' => $costo
								);

							// Crea la tarea
							if ($accion == 'create')
							{
								$dataTarea['desdefecha'] = $dataTarea['fecha'];
								$dataTarea['hastafecha'] = null;

       							$item_tarea = $this->ordentrabajo_tareaRepository->create($dataTarea);

								if ($item_tarea)
									$dataTarea['ordentrabajo_tarea_id'] = $item_tarea->id;
							}
							else // Actualiza la tarea
							{
								$dataTarea['desdefecha'] = $ordentrabajo_tarea_filtrada[0]->desdefecha;
								$dataTarea['hastafecha'] = $dataTarea['fecha'];

       							$item_tarea = $this->ordentrabajo_tareaRepository->update($dataTarea, $ordentrabajo_tarea_filtrada[0]->id);

								if ($item_tarea)
									$dataTarea['ordentrabajo_tarea_id'] = $ordentrabajo_tarea_filtrada[0]->id;
							}

							// Crea el movimiento de OT
      						$movimientoordentrabajo = $this->movimientoordentrabajoRepository->create($dataTarea);
						}
						else
						{
							throw new ModelNotFoundException("No puede grabar movimiento tarea ya existente en OT");
						}
					}
					else
					{
					}
					DB::commit();
				} catch (\Exception $e) {
					DB::rollback();
					return $e->getMessage();
				}
			}
			else
			{
				throw new ModelNotFoundException("OT Inexistente");
			}
		}

		return ['ordenes'=>$codigos_ot];
	}

	public function borraMovimientoOrdenTrabajo($id)
	{
		$fl_borro = false;

		try
		{
			// Agregar validaciones de anulacion
			$movimientoordentrabajo = self::leeMovimientoOrdenTrabajo($id);

			if ($movimientoordentrabajo)
			{
				// Lee la tarea
				$ordentrabajo_tarea = $this->ordentrabajo_tareaRepository->find($movimientoordentrabajo->ordentrabajo_tarea_id);

				if ($ordentrabajo_tarea)
				{
					// Si el movimiento es de finalizacion borra la fecha en la tarea si no borra la tarea
					if ($movimientoordentrabajo->operaciones->tipooperacion == 'F')
						$ordentrabajo_tarea = $this->ordentrabajo_tareaRepository->update(['hastafecha' => null]);
					else
						$ordentrabajo_tarea = $this->ordentrabajo_tareaRepository->delete($ordentrabajo_tarea->id);
				}

				$this->movimientoordentrabajoRepository->delete($id);
				$fl_borro = true;
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
		return ($fl_borro);
	}

	// Empaca tarea en produccion

	public function empacaTarea($request)
	{
		DB::beginTransaction();
		try
		{
			// Graba tarea 
			$data['ordentrabajo_id'] = $request['ordentrabajo_id'];
			$data['tarea_id'] = Config::get("consprod.TAREA_EMPAQUE"); // Tarea Empaque
			$data['desdefecha'] = Carbon::now();
			$data['hastafecha'] = Carbon::now();
			$data['empleado_id'] = null;
			$data['pedido_combinacion_id'] = $request['pedido_combinacion_id'];
			$data['estado'] = Config::get("consprod.TAREA_ESTADO_TERMINADA");
			$data['costo'] = 0;
			$data['usuario_id'] = Auth::user()->id;

			$ordentrabajo = $this->ordentrabajo_tareaRepository->create($data);

			DB::commit();
		} catch (\Exception $e) 
		{
			DB::rollback();
			dd($e->getMessage());
			return $e->getMessage();
		}
		// Imprime tarea de armado
		// Arma nombre de archivo
		$nombreReporte = "tmp/empaqueOT-" . $data['ordentrabajo_id'] . '.txt';

		$reporte = "";
		$reporte .= "Empaque de ORDEN DE TRABAJO NRO. ".$request['codigoordentrabajo']."\n\n";
		$reporte .= "Cliente: ".$request['cliente']."\n\n";
		$reporte .= "Articulo: ".$request['articulo']."\n\n";
		$reporte .= "Combinacion: ".$request['combinacion']."\n\n";

		$reporte .= "MEDIDAS\n";
		$medidas = json_decode($request['medidas']);
		
		foreach($medidas as $medida)
		{
			$reporte .= "Talle: ".$medida->talle." Cantidad: ".$medida->cantidad."\n";
		}
		
		$reporte .= "\nTotal pares: ".$request['pares']."\n\n\n\n\n\n\n\n\n\n\n\n\n";

		Storage::disk('local')->put($nombreReporte, $reporte);
		$path = Storage::path($nombreReporte);
		system("lp -darmado ".$path);

		Storage::disk('local')->delete($nombreReporte);
	}

	// Control de secuencia de fabricacion

	public function controlSecuencia($ordenestrabajo, $operacion_id, $tarea_id)
	{
		$arrayOrdenesTrabajo = explode(',', $ordenestrabajo);
		$secuenciaTareas = Config::get("consprod.SECUENCIA_TAREAS");
		$otConProblema = [];
		$flExiste = false;
		foreach($arrayOrdenesTrabajo as $ordentrabajo)
		{
			$ot = $this->ordentrabajoRepository->findPorCodigo($ordentrabajo);

			$tareas = self::leeTareas($ot->id);
			foreach($tareas as $tarea)
			{
				// Verifica si tiene validacion de secuencia cargada
				if (array_key_exists($tarea_id, $secuenciaTareas))		
				{
					// Busca la tarea en la secuencia
					$flExiste = false;
					foreach($secuenciaTareas[$tarea_id] as $secuencia)
					{
					//	echo($secuencia.' '.$tarea->tarea_id.' '.$tarea_id.' ');
						if ($secuencia == $tarea->tarea_id)
						{
							// Si no termino la tarea es error igual
							if ($tarea->hastafecha != null)
								$flExiste = true;
						}
						// Si la tarea ya esta cargada verifica si le falta el fin
						if ($tarea_id == $tarea->tarea_id)
						{
							if ($tarea->hastafecha == null)
								$flExiste = true;
						}
					}
				}
			}
			// Si la tarea ya fue cargada la toma con problemas
			if ($flExiste && !in_array($ordentrabajo, $otConProblema))
				$otConProblema[] = $ordentrabajo;
		}
		
		$resultado = count($otConProblema) > 0 ? 0 : 1;

		return(['resultado' => $resultado, 'ordenestrabajo' => $otConProblema]);
	}

	// Lee tareas de una OT

	public function leeTareas($ot_id)
	{
		return($this->ordentrabajo_tareaRepository->findPorOrdentrabajoId($ot_id));
	}

}
