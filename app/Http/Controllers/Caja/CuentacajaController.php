<?php

namespace App\Http\Controllers\Caja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Caja\Cuentacaja;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionCuentacaja;
use App\Repositories\Caja\CuentacajaRepositoryInterface;

class CuentacajaController extends Controller
{
	private $repository;

    public function __construct(CuentacajaRepositoryInterface $repository)
    {
        $this->repository = $repository;
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

        return view('caja.cuentacaja.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cuentas-de-caja');

        return view('caja.cuentacaja.crear');
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

        return view('caja.cuentacaja.editar', compact('data'));
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
