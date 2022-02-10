<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidacionPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Ventas\TransporteRepositoryInterface;
use App\Queries\Ventas\ClienteQueryInterface;
use App\Services\Ventas\PedidoService;
use App\Models\Configuracion\Moneda;
use App\Models\Stock\Articulo;
use App\Models\Stock\Mventa;
use App\Models\Stock\Modulo;
use App\Models\Stock\Listaprecio;
use App\Models\Ventas\Vendedor;
use App\Models\Ventas\Condicionventa;
use DB;

class PedidoController extends Controller
{
	private $pedidoService;
	private $clienteQuery;
	private $transporteRepository;

    public function __construct(PedidoService $pedidoservice,
    							TransporteRepositoryInterface $transporterepository,
								ClienteQueryInterface $clientequery
								)
    {
        $this->pedidoService = $pedidoservice;
        $this->transporteRepository = $transporterepository;
        $this->clienteQuery = $clientequery;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($cliente_id = null)
    {
        can('listar-pedidos');

		$datas = $this->pedidoService->leePedidosPendientes($cliente_id);

        return view('ventas.pedido.index', compact('datas'));
    }

	/* Lista el pedido */
	public function listarPedido($id, $cliente_id = null)
	{
		return $this->pedidoService->listaPedido($id);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-pedidos');

		$this->armaTablasVista($cliente_query, $condicionventa_query, $vendedor_query, $transporte_query,
							$mventa_query, $articulo_query, $modulo_query, $listaprecio_query, $moneda_query);

        return view('ventas.pedido.crear', compact('cliente_query', 'condicionventa_query', 'vendedor_query',
			'transporte_query', 'mventa_query', 'articulo_query', 'modulo_query', 'listaprecio_query', 'moneda_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionPedido $request)
    {
		$this->pedidoService->guardaPedido($request->all(), 'create');

    	return redirect('ventas/pedido')->with('mensaje', 'Pedido creado con exito');
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-pedidos');

    	$pedido = $this->pedidoService->leePedido($id);

		$this->armaTablasVista($cliente_query, $condicionventa_query, $vendedor_query, $transporte_query,
							$mventa_query, $articulo_query, $modulo_query, $listaprecio_query, $moneda_query);

        return view('ventas.pedido.editar', compact('pedido', 'cliente_query', 'condicionventa_query', 'vendedor_query',
			'transporte_query', 'mventa_query', 'articulo_query', 'modulo_query', 'listaprecio_query', 'moneda_query'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionPedido $request, $id)
    {
        can('actualizar-pedidos');

		$this->pedidoService->guardaPedido($request->all(), 'update', $id);

        return redirect('ventas/pedido')->with('mensaje', 'Pedido actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-pedidos');

        if ($request->ajax()) 
		{
			if ($this->pedidoService->borraPedido($id))
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
	 * Arma tablas de select para enviar a vista
	 */
	private function armaTablasVista(&$cliente_query, &$condicionventa_query, &$vendedor_query, 
				&$transporte_query, &$mventa_query, &$articulo_query, &$modulo_query, &$listaprecio_query, 
				&$moneda_query)
	{
		$cliente_query = $this->clienteQuery->allQuery(['id','nombre','codigo']);
		$condicionventa_query = Condicionventa::all();
		$vendedor_query = Vendedor::all();
		$transporte_query = $this->transporteRepository->all();
		$mventa_query = Mventa::all();

        $articulo_query = Articulo::select('id', 'sku', 'descripcion', 'mventa_id')
									->whereExists(function($query) 
									{
    									$query->select(DB::raw(1))
											->from("combinacion")
          									->whereRaw("combinacion.articulo_id=articulo.id and combinacion.estado='A'");
									})->get();
		$modulo_query = Modulo::all();
		$listaprecio_query = Listaprecio::all();
		$moneda_query = Moneda::all();
	}
}
