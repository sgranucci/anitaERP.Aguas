<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Queries\Ventas\OrdentrabajoQueryInterface;
use App\Queries\Ventas\ClienteQueryInterface;
use App\Services\Ventas\OrdentrabajoService;
use App\Queries\Stock\ArticuloQueryInterface;
use App\Repositories\Ventas\PuntoventaRepositoryInterface;
use App\Repositories\Ventas\TipotransaccionRepositoryInterface;
use App\Repositories\Ventas\IncotermRepositoryInterface;
use App\Repositories\Ventas\FormapagoRepositoryInterface;
use App\Models\Stock\Mventa;
use App\Models\Stock\Talle;
use App\Models\Stock\Combinacion;
use App\Models\Produccion\Tarea;

class OrdentrabajoController extends Controller
{
	private $ordentrabajoQuery;
	private $clienteQuery;
	private $ordentrabajoService;
	private $articuloService;
	private $puntoventaRepository;
	private $tipotransaccionRepository;
	private $incotermRepository;
	private $formpagoRepository;

    public function __construct(
    	OrdentrabajoService $ordentrabajoservice,
		OrdentrabajoQueryInterface $ordentrabajoquery,
		ClienteQueryInterface $clientequery,
		ArticuloQueryInterface $articuloquery,
		PuntoventaRepositoryInterface $puntoventarepository,
		TipotransaccionRepositoryInterface $tipotransaccionrepository,
		IncotermRepositoryInterface $incotermrepository,
		FormapagoRepositoryInterface $formpagorepository)
	{
        $this->middleware('auth');

        $this->ordentrabajoService = $ordentrabajoservice;
        $this->ordentrabajoQuery = $ordentrabajoquery;
        $this->clienteQuery = $clientequery;
        $this->articuloQuery = $articuloquery;
		$this->puntoventaRepository = $puntoventarepository;
		$this->tipotransaccionRepository = $tipotransaccionrepository;
		$this->incotermRepository = $incotermrepository;
		$this->formapagoRepository = $formpagorepository;
	}

    public function index()
    {
		can("listar-ordenes-de-trabajo");
		
		$ordentrabajo_query = $this->ordentrabajoService->leeOrdenestrabajoPendientes();

        return view('ventas.ordentrabajo.index', compact('ordentrabajo_query'));
    }

    public function indexEtiqueta()
    {
		$tipoetiqueta_enum = [
			'CAJA FOTO' => 'Etiqueta CAJA CON FOTO',
			'CUIT' => 'Etiqueta CUIT',
			'CAJA' => 'Etiqueta CAJA',
		];

		$origen_enum = [
			'ANITA' => 'Lee datos en ANITA',
			'ERP' => 'Lee datos en ANITA ERP'
		];

        return view('ventas.repetiquetaot.create', compact('tipoetiqueta_enum', 'origen_enum'));
    }

    public function crearEtiquetaOt(Request $request)
    {
		if ($request->tipoetiqueta == "CUIT")
			return $this->ordentrabajoService->listaEtiquetaCuit($request->all());
		else
			if ($request->tipoetiqueta == "CAJA")
				return $this->ordentrabajoService->listaEtiquetaCaja($request->all());
			else
				return $this->ordentrabajoService->listaEtiquetaCajaZPL($request->all());
    }

    public function generaZPL()
    {
        return view('ventas.repetiquetaot.createzpl');
    }

	public function generaEtiquetaPruebaOt()
    {
		$articulo_query = $this->articuloQuery->allQueryConCombinacion(['id', 'sku', 'descripcion', 'mventa_id'], 'descripcion');

        return view('ventas.repetiquetaot.createetiquetaprueba', compact('articulo_query'));
    }

	public function crearEtiquetaPruebaOt(Request $request)
    {
		return $this->ordentrabajoService->listaEtiquetaPruebaCajaZPL($request->all());
    }

    public function indexEmisionOt()
    {
		$tipoemision_enum = [
			'COMPLETA' => 'OT Completa',
			'STOCK' => 'OT Stock',
			'CAJA' => 'OT Caja',
		];

        return view('ventas.repemisionot.create', compact('tipoemision_enum'));
    }

    public function crearEmisionOt(Request $request)
    {
		return $this->ordentrabajoService->EmisionOt($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-ordenes-de-trabajo');

		$articulo_query = $this->articuloQuery->allQueryConCombinacion(['id', 'sku', 'descripcion', 'mventa_id'], 'descripcion');
		$mventa_query = Mventa::all();

        return view('ventas.ordentrabajo.crear', compact('articulo_query', 'mventa_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function guardar($origen, $ids, $checkOtStock, $ordentrabajo_stock_codigo, $leyenda = null)
    {
		$ids = json_decode($ids);
		
		if ($leyenda)
			$leyenda = json_decode($leyenda);

		$data = $this->ordentrabajoService->guardaOrdenTrabajo($ids, $checkOtStock, 
																$ordentrabajo_stock_codigo,
																(!$leyenda ? ' ' : $leyenda), 'create');

		if ($origen == 'pedido')
		{
        	return ['id'=>$data['id'],'nro_orden'=>$data['nro_orden']];
		}
		else
		{
			$mensaje = 'Orden de trabajo '.$data['id'].' creada con exito ';
        	return redirect('ventas/ordenestrabajo')->with('mensaje', $mensaje);
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-ordenes-de-trabajo');

    	$ordentrabajo = $this->ordentrabajoQuery->leeOrdenTrabajo($id);
	
		$mventa_id = $articulo_id = $combinacion_id = '';
		$this->armarTablasVista($cliente_query, $mventa_query, $articulo_query, $combinacion_query, $talle_query, $tarea_query, $ordentrabajo, 
								$mventa_id, $articulo_id, $combinacion_id, $puntoventa_query,
								$tipotransaccion_query, $formapago_query,
								$incoterm_query);

		$data = [];
		foreach ($ordentrabajo->ordentrabajo_combinacion_talles as $ot)
		{
			$item = $ot->pedido_combinacion_talles->pedidos_combinacion;
			
			// Arma medidas
			$medidas = [
				'talle'=>$ot->pedido_combinacion_talles->talle_id,
				'nombretalle'=>$ot->pedido_combinacion_talles->talles->nombre,
				'cantidad'=>$ot->pedido_combinacion_talles->cantidad,
				'precio'=>$ot->pedido_combinacion_talles->precio,
			];

			$id = $item->id;
			$flEncontro = false;
			
			for ($ii = 0; $ii < count($data); $ii++)
			{
				if ($data[$ii]['id'] == $id)
				{	
					$flEncontro = true;
					break;
				}
			}
		
			if (!$flEncontro)
			{
				// Lee combinacion
				$combinacion = Combinacion::find($item->combinacion_id);

				$data[] = [	
						'id'=>$item->id, 
						'codigo'=>$item->pedidos->codigo, 
						'cliente'=>$ot->clientes->nombre, 
						'cliente_id'=>$ot->clientes->id,
						'estadocliente'=>$ot->clientes->estado,
						'tiposuspensioncliente_id'=>$ot->clientes->tiposupension_id,
						'nombretiposuspensioncliente'=>$ot->clientes->tipossuspensioncliente->nombre??'',
						'articulo'=>$item->articulos->descripcion,
						'sku'=>$item->articulos->sku,
						'articulo_id'=>$item->articulos->id,
						'modulo_id'=>$item->modulo_id,
						'pares'=>$item->cantidad, 
						'combinacion_id'=>$item->combinacion_id,
						'nombre_combinacion'=>$combinacion->nombre,
						'medidas' => [$medidas],
						];
						
			}
			else
			{
				$data[$ii]['medidas'][] = $medidas;
			}
		}
		$puntoventadefault_id = cache()->get(generaKey('puntoventa'));
		$puntoventaremitodefault_id = cache()->get(generaKey('puntoventaremito'));
		$tipotransacciondefault_id = cache()->get(generaKey('tipotransaccion'));

		return view('ventas.ordentrabajo.editar', compact('ordentrabajo', 'cliente_query', 'articulo_query', 
														'combinacion_query', 'mventa_query', 'talle_query', 
														'tarea_query', 'mventa_id', 'articulo_id', 'combinacion_id', 
														'puntoventa_query', 'puntoventadefault_id', 
														'tipotransaccion_query', 'tipotransacciondefault_id',
														'data', 'puntoventaremitodefault_id',
														'formapago_query', 'incoterm_query'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        can('actualizar-ordenes-de-trabajo');

		// Se arma de request la modificacion
		$ids = implode(',', $request->ids);
		if ($request->tipoot == null)
			$checkOtStock = 'off';
		else
			$checkOtStock = 'on';
		//$data = $this->ordentrabajoService->guardaOrdenTrabajo($request['ids'], $checkOtStock, (!$request->leyenda ? ' ' : $request->leyenda), 'update');

		$data['id'] = $id;

		//$mensaje = 'Orden de trabajo '.$data['id'].' actualizada con exito ';
		$mensaje = 'FUNCION DE ACTUALIZACION NO IMPLEMENTADA';

        return redirect('ventas/ordenestrabajo')->with('mensaje', $mensaje);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-ordenes-de-trabajo');

        if ($request->ajax()) 
		{
			if ($this->ordentrabajoService->borraOrdenTrabajo($id))
				$fl_borro = true;

            if ($fl_borro) {
		       	return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

	/*
	 * Lista orden de trabajo por laser
	 */

    public function listar($id)
	{
		return $this->ordentrabajoService->listaOrdenTrabajoLaser($id);
	}

	/*
	 * Arma tablas de select para enviar a vista
	 */
	private function armarTablasVista(&$cliente_query, &$mventa_query, &$articulo_query, &$combinacion_query, &$talle_query, &$tarea_query,
										$ordentrabajo, &$mventa_id, &$articulo_id, &$combinacion_id,
										&$puntoventa_query, &$tipotransaccion_query,
										&$formapago_query, &$incoterm_query)
	{
		$cliente_query = $this->clienteQuery->allQueryporEstado(['id','nombre','codigo'], '0');//Cliente::$enumEstado['activo']);
		$mventa_query = Mventa::all();
		$talle_query = Talle::all();
        //$articulo_query = $this->articuloQuery->traeArticulosActivos();
		$articulo_query = $this->articuloQuery->allQuery(['id', 'sku', 'descripcion', 'mventa_id']);
		$tarea_query = Tarea::all();
		$puntoventa_query = $this->puntoventaRepository->all('A');
		$tipotransaccion_query = $this->tipotransaccionRepository->all(['V','C'], ['A']);
		$formapago_query = $this->formapagoRepository->all();
		$incoterm_query = $this->incotermRepository->all();
			
		if ($ordentrabajo->ordentrabajo_combinacion_talles[0]->pedido_combinacion_talles)
		{
			$mventa_id = $ordentrabajo->ordentrabajo_combinacion_talles[0]->pedido_combinacion_talles->pedidos_combinacion->articulos->mventa_id;
			$articulo_id = $ordentrabajo->ordentrabajo_combinacion_talles[0]->pedido_combinacion_talles->pedidos_combinacion->articulo_id;
			$combinacion_id = $ordentrabajo->ordentrabajo_combinacion_talles[0]->pedido_combinacion_talles->pedidos_combinacion->combinacion_id;
		}
		else
			$mventa_id = $articulo_id = $combinacion_id = '';

		// Lee las combinaciones 
		$combinacion_query = Combinacion::where('articulo_id',$articulo_id)->get();
	}

	// controla estado de orden de trabajo
	public function estadoOt($codigoordentrabajo)
	{
		return $this->ordentrabajoService->otFacturada($codigoordentrabajo, null);
	}

	// controla orden de trabajo de stock
	public function controlaOtStock($codigoordentrabajo, $articulo_id, $combinacion_id)
	{
		return $this->ordentrabajoService->controlaOtStock($codigoordentrabajo, $articulo_id, $combinacion_id);
	}
	
}
