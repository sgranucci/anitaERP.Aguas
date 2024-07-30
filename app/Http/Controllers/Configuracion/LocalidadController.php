<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Localidad;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionLocalidad;
use App\Models\Configuracion\Provincia;

class LocalidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	  	ini_set('memory_limit', '512M');
        can('listar-localidades');
        $datas = Localidad::with('provincias')->orderBy('nombre')->paginate(15);

		if ($datas->isEmpty())
		{
			$Localidad = new Localidad();
        	$Localidad->sincronizarConAnita();
	
        	$datas = Localidad::with('provincias')->orderBy('nombre')->paginate(15);
		}
        return view('configuracion.localidad.index', compact('datas'));
    }

	public function leerLocalidades($id)
    {
        return Localidad::select('id','nombre')->where('provincia_id',$id)->orderBy('nombre','asc')->get()->toArray();
    }

    public function leerCodigoPostal($id)
    {
        $cp = Localidad::select('codigopostal')->where('id',$id)->get();
        return $cp[0]->codigopostal;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-localidades');

		$provincia_query = Provincia::all();

        return view('configuracion.localidad.crear', compact('provincia_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionLocalidad $request)
    {
        $localidad = Localidad::create($request->all());

		// Graba anita
		$Localidad = new Localidad();
        $Localidad->guardarAnita($request, $localidad->id);

        return redirect('configuracion/localidad')->with('mensaje', 'Localidad creada con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-localidades');
		$provincia_query = Provincia::all();
		$data = Localidad::where('id', $id)->with('provincias:id,nombre')->first();
        return view('configuracion.localidad.editar', compact('data', 'provincia_query'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionLocalidad $request, $id)
    {
        can('actualizar-localidades');
        Localidad::findOrFail($id)->update($request->all());

		// Actualiza anita
		$Localidad = new Localidad();
        $Localidad->actualizarAnita($request, $id);

        return redirect('configuracion/localidad')->with('mensaje', 'Localidad actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-localidades');

		// Elimina anita
		$Localidad = new Localidad();
        $Localidad->eliminarAnita($id);

        if ($request->ajax()) {
            if (Localidad::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
