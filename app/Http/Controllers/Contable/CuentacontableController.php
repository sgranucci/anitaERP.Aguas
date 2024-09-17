<?php

namespace App\Http\Controllers\Contable;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contable\Cuentacontable;
use App\Models\Contable\Rubrocontable;
use App\Http\Requests\ValidacionCuentacontable;
use App\Repositories\Caja\ConceptogastoRepositoryInterface;
use App\Repositories\Contable\CuentacontableRepositoryInterface;
use App\Repositories\Configuracion\EmpresaRepositoryInterface;

class CuentacontableController extends Controller
{
    private $conceptogastoRepository;
    private $cuentacontableRepository;
    private $empresaRepository;
 
    public function __construct(ConceptogastoRepositoryInterface $conceptogastorepository,
                                CuentacontableRepositoryInterface $cuentacontablerepository,
                                EmpresaRepositoryInterface $empresarepository)
    {
        $this->conceptogastoRepository = $conceptogastorepository;
        $this->cuentacontableRepository = $cuentacontablerepository;
        $this->empresaRepository = $empresarepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cuentas-contables');

        $cuentacontables = $this->cuentacontableRepository->all();

        return view('contable.cuentacontable.index', compact('cuentacontables'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cuentas-contables');
		$rubrocontable_query = Rubrocontable::all();
        $empresa_query = $this->empresaRepository->all();
        $cuentacontable_query = $this->cuentacontableRepository->all();
        $conceptogasto_query = $this->conceptogastoRepository->all();
        $ajustamonedaextranjera_enum = CuentaContable::$enumAjustaMonedaExtranjera;

        return view('contable.cuentacontable.crear', compact('rubrocontable_query', 'empresa_query',
                                                        'conceptogasto_query', 'cuentacontable_query',
                                                        'ajustamonedaextranjera_enum'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionCuentacontable $request)
    {
        $this->cuentacontableRepository->all($request->all());

        return redirect('contable/cuentacontable/crear')->with('mensaje', 'Cuenta contable creada con exito');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-cuentas-contables');

        $data = $this->cuentacontableRepository->findOrFail($id);
		$rubrocontable_query = Rubrocontable::all();
        $empresa_query = $this->empresaRepository->all();
        $cuentacontable_query = $this->cuentacontableRepository->all();
        $conceptogasto_query = $this->conceptogastoRepository->all();
        $ajustamonedaextranjera_enum = CuentaContable::$enumAjustaMonedaExtranjera;

        return view('contable.cuentacontable.editar', compact('data', 'rubrocontable_query', 
                                                'empresa_query', 'conceptogasto_query', 'cuentacontable_query',
                                                'ajustamonedaextranjera_enum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionCuentacontable $request, $id)
    {
        can('actualizar-cuentas-contables');

        $this->cuentacontableRepository->update($request->all(), $id);

        return redirect('contable/cuentacontable')->with('mensaje', 'Cuenta actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        can('borrar-cuentas-contables');

        if ($request->ajax()) {
        	if ($this->cuentacontableRepository->delete($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        return redirect('contable/cuentacontable')->with('mensaje', 'Cuenta eliminada con exito');
    }

}
