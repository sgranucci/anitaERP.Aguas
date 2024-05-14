<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Ventas\FacturacionService;
use App\Queries\Ventas\ClienteQueryInterface;
use App\Repositories\Ventas\TipotransaccionRepository;
use App\Repositories\Ventas\TransporteRepositoryInterface;
use App\Repositories\Ventas\PuntoventaRepository;
use App\Repositories\Stock\LoteRepositoryInterface;
use App\Repositories\Ventas\PuntoventaRepositoryInterface;
use App\Repositories\Ventas\TipotransaccionRepositoryInterface;
use App\Repositories\Ventas\IncotermRepositoryInterface;
use App\Repositories\Ventas\FormapagoRepositoryInterface;
use App\Models\Stock\Mventa;
use App\Models\Stock\Depmae;
use App\Models\Stock\Modulo;
use App\Models\Stock\Listaprecio;
use App\Models\Ventas\Vendedor;
use App\Models\Ventas\Condicionventa;
use App\Exports\Ventas\FacturaExport;

class FacturacionController extends Controller
{
	private $facturacionService;
    private $tipotransaccionRepository;
    private $puntoventaRepository;
    private $loteRepository;
    private $clienteQuery;
    private $incotermRepository;
	private $formpagoRepository;
    private $transporteRepository;

    public function __construct(FacturacionService $facturacionservice,
                                LoteRepositoryInterface $loterepository,
                                ClienteQueryInterface $clientequery,
                                TipotransaccionRepository $tipotransaccionRepository,
                                PuntoventaRepository $puntoventaRepository,
                                IncotermRepositoryInterface $incotermrepository,
								FormapagoRepositoryInterface $formpagorepository,
                                TransporteRepositoryInterface $transporterepository)
    {
        $this->middleware('auth');

        $this->facturacionService = $facturacionservice;
        $this->tipotransaccionRepository = $tipotransaccionRepository;
        $this->puntoventaRepository = $puntoventaRepository;
        $this->loteRepository = $loterepository;
        $this->clienteQuery = $clientequery;
        $this->incotermRepository = $incotermrepository;
		$this->formapagoRepository = $formpagorepository;
        $this->transporteRepository = $transporterepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        can('listar-facturas');

        $busqueda = $request->busqueda;
        
		$ventas = $this->facturacionService->leePaginando($busqueda);

        $datas = ['ventas' => $ventas, 'busqueda' => $busqueda];

        return view('ventas.factura.index', $datas);
    }

    public function listar($formato = null, $busqueda = null)
    {
        can('listar-facturas'); 

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

		$ventas = $this->facturacionService->leeSinPaginar($busqueda);

        switch($formato)
        {
        case 'PDF':
            $view =  \View::make('ventas.factura.listado', compact('ventas'))
                        ->render();
            $path = storage_path('pdf/listados');
            $nombre_pdf = 'listado_factura';

            $pdf = \App::make('dompdf.wrapper');
            $pdf->setPaper('legal','portrait');
            $pdf->loadHTML($view)->save($path.'/'.$nombre_pdf.'.pdf');

            return response()->download($path.'/'.$nombre_pdf.'.pdf');
            break;

        case 'EXCEL':
            return (new FacturaExport($this->facturacionService))
                        ->parametros($busqueda)
                        ->download('factura.xlsx');
            break;

        case 'CSV':
            return (new FacturaExport($this->facturacionService))
                        ->parametros($busqueda)
                        ->download('factura.csv', \Maatwebsite\Excel\Excel::CSV);
            break;            
        }   

        $datas = ['ventas' => $ventas, 'busqueda' => $busqueda];

        return view('ventas.factura.index', $datas);       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-facturas');

        $this->armarTablasVista($deposito_query, $cliente_query,
                                $condicionventa_query, $vendedor_query, $transporte_query,
                                $formapago_query, $incoterm_query,
                                $mventa_query, $modulo_query, 
                                $listaprecio_query, 
                                $tipotransaccion_query, $puntoventa_query, $lote_query);

        $tipotransacciondefault_id = cache()->get(generaKey('tipotransaccion'));
        $puntoventadefault_id = cache()->get(generaKey('puntoventa'));

        return view('ventas.factura.crear', compact(
            'mventa_query', 'modulo_query', 'listaprecio_query', 
            'tipotransaccion_query', 'tipotransacciondefault_id', 'puntoventa_query', 'puntoventadefault_id',
            'deposito_query', 'lote_query', 'cliente_query', 'vendedor_query', 'condicionventa_query',
            'transporte_query', 'formapago_query', 'incoterm_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
		$mensaje = '';
		try
		{
            $data = $this->facturaService->generaFacturaPorItemOt($request->all());
			if (is_array($data))
				$mensaje = "Comprobante creado con exito";
			else
				if ($data)
					$mensaje = $data;
		} catch (\Exception $e)
		{
			$mensaje = $e->getMessage();
		}

        return redirect('ventas/factura')->with('mensaje', 'Comprobante actualizado con exito');		
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-movimientos-de-stock');
    	$factura = $this->facturacionService->leefactura($id);

		$this->armarTablasVista($deposito_query, $cliente_query,
                            $condicionventa_query, $vendedor_query, $transporte_query,
                            $formapago_query, $incoterm_query,
                            $mventa_query, $modulo_query, 
                            $listaprecio_query, 
                            $tipotransaccion_query, $puntoventa_query, $lote_query, $movimientostock);

		$tipotransacciondefault_id = cache()->get(generaKey('tipotransaccion'));
        $puntoventadefault_id = cache()->get(generaKey('puntoventa'));

        return view('ventas.factura.editar', compact('factura', 
			'mventa_query', 'modulo_query', 
			'listaprecio_query', 
			'tipotransaccion_query', 'tipotransacciondefault_id', 'puntoventa_query', 'puntoventadefault_id',
            'deposito_query', 'lote_query', 'cliente_query','vendedor_query', 'condicionventa_query',
            'transporte_query', 'formapago_query', 'incoterm_query'));          
    }

    /**
     * Updote the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionPuntoventa $request, $id)
    {
        can('actualizar-movimientos-de-stock');

		$this->movimientoStockService->guardaMovimientoStock($request->all(), 'update', $id);

        return redirect('venta/factura')->with('mensaje', 'Factura actualizada con éxito');       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        
    }

    public function facturarItemOt(Request $request)
    {
        return $this->facturacionService->generaFacturaPorItemOt($request->all());
    }

    /*
	 * Arma tablas de select para enviar a vista
	 */
	private function armarTablasVista(&$deposito_query, &$cliente_query,
                &$condicionventa_query, &$vendedor_query, &$transporte_query,
                &$formapago_query, &$incoterm_query,
                &$mventa_query, &$modulo_query, &$listaprecio_query, 
                &$tipotransaccion_query, &$puntoventa_query, &$lote_query)
    {
        $mventa_query = Mventa::all();
        $tipotransaccion_query = $this->tipotransaccionRepository->all(['V', 'C'], ['A']);
        $puntoventa_query = $this->puntoventaRepository->all();
        $deposito_query = Depmae::all();
        $cliente_query = $this->clienteQuery->allQueryCargaPedido(['id','nombre','codigo']);
        $vendedor_query = Vendedor::all();
		$vendedor_query->prepend((object) ['id'=>'0','nombre'=>'Primero']);
		$vendedor_query->push((object) ['id'=>'99999999','nombre'=>'Ultimo']);
        $condicionventa_query = Condicionventa::all();
		$vendedor_query = Vendedor::orderBy('nombre','ASC')->get();
		$transporte_query = $this->transporteRepository->all();
        $formapago_query = $this->formapagoRepository->all();
		$incoterm_query = $this->incotermRepository->all();
    
        $modulo_query = Modulo::all();
        $listaprecio_query = Listaprecio::all();
        $lote_query = $this->loteRepository->all();
    }
}
