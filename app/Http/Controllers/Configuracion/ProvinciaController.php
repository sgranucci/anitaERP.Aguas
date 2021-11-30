<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Provincia;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionProvincia;
use App\Models\Configuracion\Pais;

class ProvinciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-provincias');
        $datas = Provincia::with('paises')->orderBy('id')->get();

		if ($datas->isEmpty())
		{
			$Provincia = new Provincia();
        	$Provincia->sincronizarConAnita();
	
        	$datas = Provincia::with('paises')->orderBy('id')->get();
		}

        return view('configuracion.provincia.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-provincias');

		$pais_query = Pais::all();

        return view('configuracion.provincia.crear', compact('pais_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionProvincia $request)
    {
        $provincia = Provincia::create($request->all());

		// Graba anita
		$Provincia = new Provincia();
        $Provincia->guardarAnita($request, $provincia->id);

        return redirect('configuracion/provincia')->with('mensaje', 'Provincia creada con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-provincias');
		$pais_query = Pais::all();
		$data = Provincia::where('id', $id)->with('paises:id,nombre')->first();
        return view('configuracion.provincia.editar', compact('data', 'pais_query'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionProvincia $request, $id)
    {
        can('actualizar-provincias');
        Provincia::findOrFail($id)->update($request->all());

		// Actualiza anita
		$Provincia = new Provincia();
        $Provincia->actualizarAnita($request, $id);

        return redirect('configuracion/provincia')->with('mensaje', 'Provincia actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-provincias');

		// Elimina anita
		$Provincia = new Provincia();
        $Provincia->eliminarAnita($id);

        if ($request->ajax()) {
            if (Provincia::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
