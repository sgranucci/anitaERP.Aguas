<?php

namespace App\Http\Controllers\Contable;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contable\Cuentacontable;
use App\Models\Contable\Rubrocontable;
use App\Models\Configuracion\Empresa;
use App\Http\Requests\ValidacionCuentacontable;

class CuentacontableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cuentas-contables');
        $cuentacontables = Cuentacontable::getCuentacontable();

		if (count($cuentacontables) == 0)
		{
			$Cuentacontable = new Cuentacontable();
        	$Cuentacontable->sincronizarConAnita();

        	$cuentacontables = Cuentacontable::getCuentacontable();
		}

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
		$empresa_query = Empresa::all();

        return view('contable.cuentacontable.crear', compact('rubrocontable_query', 'empresa_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionCuentacontable $request)
    {
        Cuentacontable::create($request->all());

		// Graba anita
		$Cuentacontable = new Cuentacontable();
        $Cuentacontable->guardarAnita($request);

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

        $data = Cuentacontable::findOrFail($id);
		$rubrocontable_query = Rubrocontable::all();
		$empresa_query = Empresa::all();

        return view('contable.cuentacontable.editar', compact('data', 'rubrocontable_query', 'empresa_query'));
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
        Cuentacontable::findOrFail($id)->update($request->all());

		// Actualiza anita
		$Cuentacontable = new Cuentacontable();
        $Cuentacontable->actualizarAnita($request);

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
        Cuentacontable::destroy($id);
        return redirect('contable/cuentacontable')->with('mensaje', 'Cuenta eliminada con exito');
    }

    public function guardarOrden(Request $request)
    {
        can('borrar-cuentas-contables');

		// Elimina anita
		$Cuentacontable = new Cuentacontable();
        $Cuentacontable->eliminarAnita($request->codigo);

        if ($request->ajax()) {
            $cuentacontable = new Cuentacontable;
            $cuentacontable->guardarOrden($request->cuentacontable);
            return response()->json(['respuesta' => 'ok']);
        } else {
            abort(404);
        }
    }
}
