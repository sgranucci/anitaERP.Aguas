<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ventas\Cliente;
use App\Models\Ventas\Cliente_Entrega;
use App\Models\Ventas\Cliente_Archivo;
use App\Models\Ventas\Zonavta;
use App\Models\Ventas\Subzonavta;
use App\Models\Ventas\Vendedor;
use App\Models\Ventas\Transporte;
use App\Models\Ventas\Condicionventa;
use App\Models\Stock\Listaprecio;
use App\Models\Contable\Cuentacontable;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Localidad;
use App\Models\Configuracion\Provincia;
use App\Models\Configuracion\Condicioniva;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionCliente;
use App\Repositories\Ventas\ClienteRepositoryInterface;
use App\Repositories\Ventas\Cliente_EntregaRepositoryInterface;
use App\Repositories\Ventas\Cliente_ArchivoRepositoryInterface;
use App\Queries\Ventas\ClienteQueryInterface;
use App\Queries\Ventas\Cliente_EntregaQueryInterface;
use App\Services\Configuracion\IIBBService;
use Carbon\Carbon;

class ClienteController extends Controller
{
	private $clienteRepository;
	private $cliente_entregaRepository;
	private $cliente_archivoRepository;
	private $iibbService;
	private $query;
	private $cliente_entregaQuery;

    public function __construct(
		ClienteRepositoryInterface $clienteRepository, 
		Cliente_EntregaRepositoryInterface $cliente_entregaRepository, 
		Cliente_ArchivoRepositoryInterface $cliente_archivoRepository, 
		IIBBService $iibbService,
		ClienteQueryInterface $query,
		Cliente_EntregaQueryInterface $cliente_entregaquery)
    {
        $this->clienteRepository = $clienteRepository;
        $this->cliente_entregaRepository = $cliente_entregaRepository;
        $this->cliente_archivoRepository = $cliente_archivoRepository;
        $this->iibbService = $iibbService;

        $this->query = $query;
        $this->cliente_entregaQuery = $cliente_entregaquery;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-clientes');

        $hay_clientes = $this->query->first();

		if (!$hay_clientes)
			$this->clienteRepository->sincronizarConAnita();

		$datas = $this->query->all();

        return view('ventas.cliente.index', compact('datas'));
    }

	public function leerCliente_Entrega($cliente_id)
    {
        return $this->cliente_entregaQuery->traeCliente_EntregaporCliente_Id($cliente_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-clientes');

		$this->armaTablasVista($pais_query, $provincia_query, $condicioniva_query, $zonavta_query,
        	$subzonavta_query, $vendedor_query, $transporte_query, $condicionventa_query, $listaprecio_query,
        	$cuentacontable_query, $retieneiva_enum, $condicioniibb_enum, $vaweb_enum, $estado_enum, '', $tasaarba,
			$tasacaba, 'crear'); 

        return view('ventas.cliente.crear', compact('pais_query', 'provincia_query',
			'condicioniva_query', 'zonavta_query', 'subzonavta_query', 'vendedor_query', 'transporte_query',
			'condicionventa_query', 'listaprecio_query', 'retieneiva_enum', 'condicioniibb_enum', 'cuentacontable_query',
			'vaweb_enum', 'tasaarba', 'tasacaba', 'estado_enum'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionCliente $request)
    {
		$cliente = $this->clienteRepository->create($request->all());

		// Guarda tablas asociadas
		if ($cliente)
		{
			$cliente_entrega = $this->cliente_entregaRepository->create($request->all());

        	$cliente_archivo = $this->cliente_archivoRepository->create($request);
		}

        return redirect('ventas/cliente')->with('mensaje', 'Cliente creado con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-clientes');
        $data = $this->clienteRepository->findOrFail($id);

		$this->armaTablasVista($pais_query, $provincia_query, $condicioniva_query, $zonavta_query,
        	$subzonavta_query, $vendedor_query, $transporte_query, $condicionventa_query, $listaprecio_query,
        	$cuentacontable_query, $retieneiva_enum, $condicioniibb_enum, $vaweb_enum, $estado_enum, 
			$data->nroinscripcion, $tasaarba, $tasacaba, 'editar'); 

        return view('ventas.cliente.editar', compact('data', 'pais_query', 'provincia_query',
			'condicioniva_query', 'zonavta_query', 'subzonavta_query', 'vendedor_query', 'transporte_query',
			'condicionventa_query', 'listaprecio_query', 'retieneiva_enum', 'condicioniibb_enum', 'cuentacontable_query',
			'vaweb_enum', 'tasaarba', 'tasacaba', 'estado_enum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionCliente $request, $id)
    {
        can('actualizar-clientes');

		// Graba cliente
        $this->clienteRepository->update($request->all(), $id);

		// Graba lugares de entrega
        $this->cliente_entregaRepository->update($request->all(), $id);

		// Graba archivos asociados
        $this->cliente_archivoRepository->update($request, $id);

        return redirect('ventas/cliente')->with('mensaje', 'Cliente actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-clientes');

		$cliente = $this->clienteRepository->find($id);

		if ($cliente)
		{
			$codigo = $cliente->codigo;
	
        	if ($request->ajax()) {
				$cliente = $this->clienteRepository->delete($id);
				$cliente_entrega = $this->cliente_entregaRepository->delete($id, $codigo);
				$cliente_archivo = $this->cliente_archivoRepository->delete($id, $codigo);
        		if ($cliente) {
                	return response()->json(['mensaje' => 'ok']);
            	} else {
                	return response()->json(['mensaje' => 'ng']);
            	}
        	} else {
            	abort(404);
        	}
		}
		else
            return response()->json(['mensaje' => 'ng']);
    }

	private function armaTablasVista(&$pais_query, &$provincia_query, &$condicioniva_query, &$zonavta_query,
        	&$subzonavta_query, &$vendedor_query, &$transporte_query, &$condicionventa_query, &$listaprecio_query,
        	&$cuentacontable_query, &$retieneiva_enum, &$condicioniibb_enum, &$vaweb_enum, &$estado_enum, 
			$nroinscripcion, &$tasaarba, &$tasacaba, $funcion)
	{
        $pais_query = Pais::orderBy('nombre')->get();
        $provincia_query = Provincia::orderBy('nombre')->get();
        $condicioniva_query = Condicioniva::orderBy('nombre')->get();
        $zonavta_query = Zonavta::orderBy('nombre')->get();
        $subzonavta_query = SubZonavta::orderBy('nombre')->get();
        $vendedor_query = Vendedor::orderBy('nombre')->get();
        $transporte_query = Transporte::orderBy('nombre')->get();
        $condicionventa_query = Condicionventa::orderBy('nombre')->get();
        $listaprecio_query = Listaprecio::orderBy('nombre')->get();
        $cuentacontable_query = Cuentacontable::orderBy('nombre')->get();
		$retieneiva_enum = Cliente::$enumRetieneiva;
		$condicioniibb_enum = Cliente::$enumCondicioniibb;
		$vaweb_enum = Cliente::$enumVaweb;
		$estado_enum = Cliente::$enumEstado;

		if ($funcion == 'editar')
		{
			$tasaarba = $this->iibbService->leeTasaPercepcion($nroinscripcion, '902');
			$tasacaba = $this->iibbService->leeTasaPercepcion($nroinscripcion, '901');

			if ($tasaarba == '')
				$tasaarba = 'No esta en padron';
			if ($tasacaba == '')
				$tasacaba = 'No esta en padron';
		}
		else
			$tasaarba = $tasacaba = '';
	}
}
