<?php

namespace App\Http\Controllers\Caja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Empresa;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionCuentacaja;
use App\Models\Caja\Cuentacaja;
use App\Models\Contable\Cuentacontable;
use App\Repositories\Caja\CuentacajaRepositoryInterface;
use App\Repositories\Caja\BancoRepositoryInterface;

class CuentacajaController extends Controller
{
	private $repository;
    private $bancoRepository;

    public function __construct(CuentacajaRepositoryInterface $repository,
                                BancoRepositoryInterface $bancorepository)
    {
        $this->repository = $repository;
        $this->bancoRepository = $bancorepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cuentas-de-caja');
		$datas = $this->repository->all();
        $tipocuenta_enum = Cuentacaja::$enumTipocuenta;

        return view('caja.cuentacaja.index', compact('datas', 'tipocuenta_enum'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cuentas-de-caja');
        $empresa_query = Empresa::orderBy('nombre')->get();
        $banco_query = $this->bancoRepository->all();
        $cuentacontable_query = Cuentacontable::orderBy('nombre')->get();
        $tipocuenta_enum = Cuentacaja::$enumTipocuenta;

        return view('caja.cuentacaja.crear', compact('empresa_query', 'banco_query', 'cuentacontable_query',
                                                    'tipocuenta_enum'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionCuentacaja $request)
    {
		$this->repository->create($request->all());

        return redirect('caja/cuentacaja')->with('mensaje', 'Cuenta de caja creada con éxito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-cuentas-de-caja');
        $data = $this->repository->findOrFail($id);
        $empresa_query = Empresa::orderBy('nombre')->get();
        $banco_query = $this->bancoRepository->all();
        $cuentacontable_query = Cuentacontable::orderBy('nombre')->get();
        $tipocuenta_enum = Cuentacaja::$enumTipocuenta;

        return view('caja.cuentacaja.editar', compact('data', 'empresa_query', 'banco_query', 'cuentacontable_query',   
                                                    'tipocuenta_enum'));
    }

    /**
     * Updote the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionCuentacaja $request, $id)
    {
        can('actualizar-cuentas-de-caja');

        $this->repository->update($request->all(), $id);

        return redirect('caja/cuentacaja')->with('mensaje', 'Cuenta de caja actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-cuentas-de-caja');

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
