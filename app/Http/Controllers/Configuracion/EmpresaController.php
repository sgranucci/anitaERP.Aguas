<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Empresa;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionEmpresa;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-empresas');
        $datas = Empresa::orderBy('id')->get();

		if ($datas->isEmpty())
		{
			$Empresa = new Empresa();
        	$Empresa->sincronizarConAnita();
	
        	$datas = Empresa::orderBy('id')->get();
		}

        return view('configuracion.empresa.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-empresas');
        return view('configuracion.empresa.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionEmpresa $request)
    {
        $empresa = Empresa::create($request->all());

		// Graba anita
		$Empresa = new Empresa();
        $Empresa->guardarAnita($request, $empresa->id);

        return redirect('configuracion/empresa')->with('mensaje', 'Empresa creada con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-empresas');
        $data = Empresa::findOrFail($id);
        return view('configuracion.empresa.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionEmpresa $request, $id)
    {
        can('actualizar-empresas');
        Empresa::findOrFail($id)->update($request->all());

		// Actualiza anita
		$Empresa = new Empresa();
        $Empresa->actualizarAnita($request, $id);

        return redirect('configuracion/empresa')->with('mensaje', 'Empresa actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-empresas');

		$empresa = Empresa::findOrFail($id);

		// Elimina anita
		$Empresa = new Empresa();
        $Empresa->eliminarAnita($empresa->codigo);

        if ($request->ajax()) {
            if (Empresa::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
