<?php
namespace App\Services\Stock;

use App\Repositories\Stock\MovimientoStockRepositoryInterface;
use App\Services\Stock\Articulo_MovimientoService;
use App\Repositories\Ventas\TipotransaccionRepositoryInterface;
use App\Models\Stock\Talle;
use Auth;
use DB;

class MovimientoStockService 
{
	protected $movimientostockRepository;
	protected $tipotransaccionRepository;
	protected $articulo_movimientoService;

    public function __construct(MovimientoStockRepositoryInterface $movimientostockrepository,
								Articulo_MovimientoService $articulo_movimientoservice,
								TipotransaccionRepositoryInterface $tipotransaccionrepository
								)
    {
        $this->movimientostockRepository = $movimientostockrepository;
		$this->articulo_movimientoService = $articulo_movimientoservice;
		$this->tipotransaccionRepository = $tipotransaccionrepository;
    }

	public function estadoEnum()
	{
		return $this->movimientostockRepository->estadoEnum(); 
	}

	public function all()
	{
        $movimientostock = $this->movimientostockRepository->all();

        return $movimientostock;
	}

	public function leeMovimientoStock($id)
	{
        $movimientostock = $this->movimientostockRepository->find($id);

		return $movimientostock;
	}

	public function guardaMovimientoStock($data, $funcion, $id = null)
	{
	  	ini_set('memory_limit', '512M');

		$estadoEnum = Self::estadoEnum();
		$data['estado'] = array_search('Activa', $estadoEnum);
		$data['usuario_id'] = Auth::user()->id;
		$data['descuentointegrado'] = ' ';

		if (!array_key_exists('leyenda',$data))
			$data['leyenda'] = ' ';
		DB::beginTransaction();
		try 
		{
			// Lee el tipo de transaccion
			$tipotransaccion = $this->tipotransaccionRepository->find($data['tipotransaccion_id']);

			if (!$tipotransaccion)
				throw new Exception('No puede leer tipo de transacción');

			if ($funcion == 'create')
			{
				$movimientostock = $this->movimientostockRepository->latest('id');

				// Numera desde 100000 para que no se junte con las OT
				if ($data['lote'] == 'LOTE DE ALTA')
				{
					$lote = 500000;
					if ($movimientostock)
						$lote = $movimientostock->id+500000;
					$data['lote'] = $lote + 1;
				}
				
				$id = 0;
				if ($movimientostock)
					$id = $movimientostock->id;

				$data['codigo'] = $id + 1;

				// Guarda maestro de movimientostocks 
				$movimientostock = $this->movimientostockRepository->create($data);
			}
			else
			{
				// Actualiza maestro de movimientostocks
				$movimientostock = $this->movimientostockRepository->update($data, $id);
			}

			// Guarda items
			if ($movimientostock)
			{
				$movimientostock_id = ($funcion == 'update' ? $id : $movimientostock->id);

				// Borra los registros de movimientos antes de grabar nuevamente
				if ($funcion == 'update')
				{
					$this->articulo_movimientoService->deletePorMovimientoStockId($movimientostock_id);
				}
				$articulos = $data['articulos_id'];
				$combinaciones = $data['combinaciones_id'];
				$modulos = $data['modulos_id'];
				$numeroitems = $data['items'];
				$cantidades = $data['cantidades'];
				$precios = $data['precios'];
				$listaprecios = $data['listasprecios_id'];
				$incluyeimpuestos = $data['incluyeimpuestos'];
				$monedas = $data['monedas_id'];
				$descuentos = $data['descuentos'];
				$loteids = $data['loteids'];
				$medidas = $data['medidas'];
				
				// Graba items
				$dataArticuloMovimiento = [];
				for ($i = 0; $i < count($articulos); $i++)
				{
					$dataArticuloMovimiento = [
						'fecha' => $data['fecha'],
						'fechajornada' => $data['fecha'],
						'tipotransaccion_id' => $data['tipotransaccion_id'],
						'movimientostock_id' => $movimientostock_id,
						'deposito_id' => $data['deposito_id'],
						'venta_id' => null,
						'pedido_combinacion_id' => null,
						'ordentrabajo_id' => null,
						'lote' => $data['lote'],
						'articulo_id' => $articulos[$i],
						'combinacion_id' => $combinaciones[$i],
						'modulo_id' => $modulos[$i],
						'concepto' => $tipotransaccion->nombre,
						'cantidad' => $cantidades[$i],
						'precio' => $precios[$i],
						'costo' => 0,
						'descuento' => $descuentos[$i],
						'descuentointegrado' => null,
						'moneda_id' => $monedas[$i],
						'incluyeimpuesto' => $incluyeimpuestos[$i],
						'listaprecio_id' => $listaprecios[$i],
						'loteimportacion_id' => $data['loteimportacion_id']
					];
					$dataTalle = [];
					$jtalles = json_decode($medidas[$i]);
					foreach($jtalles as $medida)
					{
						$dataTalle[] = [
							'id' => null,
							'talle_id' => $medida->talle_id,
							'cantidad' => $medida->cantidad*($tipotransaccion->signo == 'S' ? 1 : -1),
							'precio' => $precios[$i],
						];
					}
					
					$articulo_movimiento = $this->articulo_movimientoService->
									guardaArticuloMovimiento('create',
									$dataArticuloMovimiento, $dataTalle);
				}
			}
			DB::commit();
		} catch (\Exception $e) 
		{
			DB::rollback();
			dd($e->getMessage());
			return $e->getMessage();
		}
		
		return ['id'=>$movimientostock_id, 'codigo'=>$data['codigo']];
	}

	public function borraMovimientoStock($id)
	{
		$movimientostock = $this->movimientostockRepository->deletePorId($id);

		$this->articulo_movimientoService->deletePorMovimientoStockId($id);

		return $movimientostock;
	}


}
