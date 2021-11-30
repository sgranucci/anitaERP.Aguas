<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Impuesto;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionImpuesto;
use Carbon\Carbon;

class ImpuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-impuestos');
        $datas = Impuesto::orderBy('id')->get();

		if ($datas->isEmpty())
		{
			$Impuesto = new Impuesto();
        	$Impuesto->sincronizarConAnita();
	
        	$datas = Impuesto::orderBy('id')->get();
		}

        return view('configuracion.impuesto.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-impuestos');
        return view('configuracion.impuesto.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionImpuesto $request)
    {
		$fechavigencia = Carbon::createFromFormat('d-m-Y', $request->fechavigencia);

        $impuesto = Impuesto::create([
       		"nombre" => $request->nombre,
        	"valor" => $request->valor,
			"fechavigencia" => $fechavigencia
        ]);

		// Graba anita
		$Impuesto = new Impuesto();
        $Impuesto->guardarAnita($request, $impuesto->id);

        return redirect('configuracion/impuesto')->with('mensaje', 'Impuesto creado con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-impuestos');
        $data = Impuesto::findOrFail($id);
        return view('configuracion.impuesto.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionImpuesto $request, $id)
    {
        can('actualizar-impuestos');
		$fechavigencia = Carbon::createFromFormat('d-m-Y', $request->fechavigencia);

        Impuesto::where('id', $id)->update([
       		"nombre" => $request->nombre,
        	"valor" => $request->valor,
			"fechavigencia" => $fechavigencia
        ]);

		// Actualiza anita
		$Impuesto = new Impuesto();
        $Impuesto->actualizarAnita($request, $id);

        return redirect('configuracion/impuesto')->with('mensaje', 'Impuesto actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-impuestos');

		// Elimina anita
		$Impuesto = new Impuesto();
        $Impuesto->eliminarAnita($id);

        if ($request->ajax()) {
            if (Impuesto::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
