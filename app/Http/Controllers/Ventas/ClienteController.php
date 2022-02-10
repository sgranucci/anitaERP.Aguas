<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ventas\Cliente;
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
use App\Queries\Ventas\ClienteQueryInterface;
use App\Services\Configuracion\IIBBService;

class ClienteController extends Controller
{
	private $repository;
	private $iibbService;
	private $query;

    public function __construct(ClienteRepositoryInterface $repository, IIBBService $iibbService,
		ClienteQueryInterface $query)
    {
        $this->repository = $repository;
        $this->iibbService = $iibbService;
        $this->query = $query;
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
			$this->repository->sincronizarConAnita();

		$datas = $this->query->all();

        return view('ventas.cliente.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-clientes');

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
		$tasaarba = '';
		$tasacaba = '';

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
		$this->repository->create($request->all());

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
        $data = $this->repository->findOrFail($id);

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
		$tasaarba = $this->iibbService->leeTasaPercepcion($data->nroinscripcion, '902');
		if ($tasaarba == '')
			$tasaarba = 'No esta en padron';
		$tasacaba = $this->iibbService->leeTasaPercepcion($data->nroinscripcion, '901');
		if ($tasacaba == '')
			$tasacaba = 'No esta en padron';

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
        $this->repository->update($request->all(), $id);

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

        if ($request->ajax()) {
        	if ($this->repository->delete($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
