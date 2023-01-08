<?php
namespace App\Services\Stock;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Repositories\Stock\Articulo_MovimientoRepositoryInterface;
use App\Repositories\Stock\Articulo_Movimiento_TalleRepositoryInterface;
use App\Repositories\Ventas\TipotransaccionRepositoryInterface;
use App\Repositories\Ventas\Ordentrabajo_TareaRepositoryInterface;
use App\Queries\Stock\Articulo_MovimientoQueryInterface;
use App\Models\Stock\Modulo;
use App\Models\Stock\Talle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Articulo_MovimientoService 
{
	protected $articulo_movimientoRepository;
	protected $articulo_movimiento_talleRepository;
	protected $tipotransaccionRepository;
	protected $articulo_movimientoQuery;
	protected $ordentrabajo_tareaRepository;

	public function __construct(
								Articulo_MovimientoRepositoryInterface $articulo_movimientorepository,
								Articulo_Movimiento_TalleRepositoryInterface $articulo_movimiento_tallerepository,
								TipotransaccionRepositoryInterface $tipotransaccionrepository,
								Ordentrabajo_tareaRepositoryInterface $ordentrabajo_tarearepository,
								Articulo_MovimientoQueryInterface $articulo_movimientoquery
								)
    {
		$this->articulo_movimientoRepository = $articulo_movimientorepository;
		$this->articulo_movimiento_talleRepository = $articulo_movimiento_tallerepository;
		$this->articulo_movimientoQuery = $articulo_movimientoquery;
		$this->tipotransaccionRepository = $tipotransaccionrepository;
		$this->ordentrabajo_tareaRepository = $ordentrabajo_tarearepository;
    }
	
	public function guardaArticuloMovimiento($funcion, $dataMovimiento, $dataTalle)
	{
		// Lee tipo de transaccion
		$tipotransaccion = $this->tipotransaccionRepository->find($dataMovimiento['tipotransaccion_id']);

		// No usa transacciones porque se llama desde otro servicio con transaccion activa
		if ($tipotransaccion)
		{
			$dataMovimiento['cantidad'] = $dataMovimiento['cantidad'] * ($tipotransaccion->signo == 'S' ? 1 : -1);

			$articulo_movimiento = $this->articulo_movimientoRepository->create($dataMovimiento);
			if ($articulo_movimiento)
			{
				foreach($dataTalle as $talle)
				{
					$data = [];
					$data['articulo_movimiento_id'] = $articulo_movimiento->id;
					$data['pedido_combinacion_talle_id'] = $talle['id'];
					$data['talle_id'] = $talle['talle_id'];
					$data['cantidad'] = $talle['cantidad']*($tipotransaccion->signo == 'S' ? 1 : -1);
					$data['precio'] = $talle['precio'];

					$this->guardaArticuloMovimientoTalle($dataMovimiento['pedido_combinacion_id'], $data);
				}
			}
			else
			{
				throw new Exception('No pudo grabar movimiento de stock del articulo.');
			}
		}
		else
		{
			throw new Exception('No encontro tipo de transaccion.');
		}
	}

	// Actualiza movimiento por pedido_combinacion_id
	public function guardaArticuloMovimientoPorPedidoCombinacionId($pedido_combinacion_id, $data)
	{
		if (array_key_exists('cantidad', $data))
		{
			$articulo_movimiento = $this->articulo_movimientoRepository->findPorPedidoCombinacionId($pedido_combinacion_id);
		
			if ($articulo_movimiento)
			{
				$tipotransaccion = $this->tipotransaccionRepository->find($articulo_movimiento->tipotransaccion_id);

				$data['cantidad'] = $data['cantidad']*($tipotransaccion->signo == 'S' ? 1 : -1);	
			}
		}
		return $this->articulo_movimientoRepository->updatePorPedidoCombinacionId($pedido_combinacion_id, $data);
	}

	// Guarda articulo_movimiento_talle
	public function guardaArticuloMovimientoTalle($pedido_combinacion_id, $data)
	{
		// Busca id de articulo_movimiento
		if (!array_key_exists('articulo_movimiento_id', $data))
		{
			$articulo_movimiento = $this->articulo_movimientoRepository->findPorPedidoCombinacionId($pedido_combinacion_id);

			// Lee tipo de transaccion
			$articulo_movimiento_talle = '';
			if ($articulo_movimiento)
			{
				$data['articulo_movimiento_id'] = $articulo_movimiento->id;

				$tipotransaccion = $this->tipotransaccionRepository->find($articulo_movimiento->tipotransaccion_id);

				if (array_key_exists('cantidad', $data))
					$data['cantidad'] = $data['cantidad']*($tipotransaccion->signo == 'S' ? 1 : -1);	
				
				$articulo_movimiento_talle = $this->articulo_movimiento_talleRepository->create($data);
			}
			else
				throw new Exception('No encontro movimiento en articulo_movimiento.');
		}
		else
			$articulo_movimiento_talle = $this->articulo_movimiento_talleRepository->create($data);

		return $articulo_movimiento_talle;
	}

	// Genera datos reporte stock de OT
	public function generaDatosRepStockOt($estado, $mventa_id, $desdearticulo, $hastaarticulo,
										$desdelinea_id, $hastalinea_id)
	{
		// Lee informacion del listado
		$data = $this->articulo_movimientoQuery->generaDatosRepStockOt($estado, $mventa_id,
				$desdearticulo, $hastaarticulo,
				$desdelinea_id, $hastalinea_id);

		// Arma el reporte
		$datas = [];
		$medidas = [];
		$anterLote = '';
		$anterSku = '';
		$anterCodigoCombinacion = '';
		$anterModulo_Id = 0;
		foreach ($data as $movimiento)
		{
			// Realiza corte
			if ($anterSku != $movimiento['sku'] ||
				$anterCodigoCombinacion != $movimiento['codigocombinacion'] ||
				$anterLote != $movimiento['lote'] ||
				$anterModulo_Id != $movimiento['modulo_id'])
			{
				if ($anterSku != '')
				{
					$datas[] = [
								'foto' => $foto,
								'nombrelinea' => $nombreLinea,
								'sku' => $sku,
								'nombrecombinacion' => $nombreCombinacion,
								'lote' => $lote,
								'precio' => $precio,
								'situacion' => $situacion,
								'modulo_id' => $modulo_id,
								'cantidadmodulo' => $cantidadModulo,
								'modulo' => $modulo,
								'medidas' => $medidas
					];
				}
				$anterSku = $movimiento['sku']; 
				$anterCodigoCombinacion = $movimiento['codigocombinacion'];
				$anterLote = $movimiento['lote'];
				$anterModulo_Id = $movimiento['modulo_id'];

				$foto = $movimiento['foto'];
				$nombreLinea = $movimiento['nombrelinea'];
				$sku = $movimiento['sku'];
				$nombreCombinacion = $movimiento['nombrecombinacion'];
				$lote = $movimiento['lote'];
				$precio = $movimiento['precio'];

				// Lee tareas de la OT para ver situacion
				$situacion = 'Pendiente de producción';
				$ordentrabajo_tarea = $this->ordentrabajo_tareaRepository->findPorOrdentrabajoId($movimiento['ordentrabajo_id']);
				if ($ordentrabajo_tarea)
				{
					$situacion = 'En producción';

					foreach($ordentrabajo_tarea as $tarea)
					{
						if ($tarea->tarea_id == config('consprod.TAREA_TERMINADA'))
							$situacion = 'ENTREGA INMEDIATA';
					}
				}
				
				$modulo_id = $movimiento['modulo_id'];
				$medidas = [];

				// Lee el modulo correspondiente
				$modulo_talle = Modulo::where('id', $movimiento['modulo_id'])->with('talles')->get();
				$modulo = [];
				$cantidadModulo = 0;
				foreach($modulo_talle[0]->talles as $unModulo)
				{
					$talle = Talle::find($unModulo->pivot->talle_id);

					if ($talle)
					{
						$modulo[] = ['medida' => $unModulo->nombre,
									'cantidad' => $unModulo->pivot->cantidad];
						$cantidadModulo += $unModulo->pivot->cantidad;
					}
				}
			}
			// Acumula medidas
			for ($ii = 0, $flEncontro = false; $ii < count($medidas); $ii++)
			{
				if ($medidas[$ii]['medida'] == $movimiento['nombretalle'])
				{
					$flEncontro = true;
					$medidas[$ii]['cantidad'] += $movimiento['cantidad'];
				}
			}
			if (!$flEncontro)
				$medidas[] = ['medida' => $movimiento['nombretalle'], 'cantidad' => $movimiento['cantidad']];
		}
		if ($anterSku != '')
		{
			$datas[] = [
						'foto' => $foto,
						'nombrelinea' => $nombreLinea,
						'sku' => $sku,
						'nombrecombinacion' => $nombreCombinacion,
						'lote' => $lote,
						'precio' => $precio,
						'situacion' => $situacion,
						'modulo_id' => $modulo_id,
						'cantidadmodulo' => $cantidadModulo,
						'modulo' => $modulo,
						'medidas' => $medidas
			];
		}
		return $datas;
	}
}

