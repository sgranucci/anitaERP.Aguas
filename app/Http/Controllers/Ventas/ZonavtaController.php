<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ventas\Zonavta;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionZonavta;

class ZonavtaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-zonas-de-venta');
        $datas = Zonavta::orderBy('id')->get();

		if ($datas->isEmpty())
		{
			$Zonavta = new Zonavta();
        	$Zonavta->sincronizarConAnita();
	
        	$datas = Zonavta::orderBy('id')->get();
		}

        return view('ventas.zonavta.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-zonas-de-venta');
        return view('ventas.zonavta.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionZonavta $request)
    {
        $zonavta = Zonavta::create($request->all());

		// Graba anita
		$Zonavta = new Zonavta();
        $Zonavta->guardarAnita($request, $zonavta->id);

        return redirect('ventas/zonavta')->with('mensaje', 'Zonavta creada con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-zonas-de-venta');
        $data = Zonavta::findOrFail($id);
        return view('ventas.zonavta.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionZonavta $request, $id)
    {
        can('actualizar-zonas-de-venta');
        Zonavta::findOrFail($id)->update($request->all());

		// Actualiza anita
		$Zonavta = new Zonavta();
        $Zonavta->actualizarAnita($request, $id);

        return redirect('ventas/zonavta')->with('mensaje', 'Zonavta actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-zonas-de-venta');

		// Elimina anita
		$Zonavta = new Zonavta();
        $Zonavta->eliminarAnita($id);

        if ($request->ajax()) {
            if (Zonavta::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
