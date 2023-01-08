<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ventas\Vendedor;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionVendedor;

class VendedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-vendedores');
        $datas = Vendedor::orderBy('id')->get();

		if ($datas->isEmpty())
		{
			$Vendedor = new Vendedor();
        	$Vendedor->sincronizarConAnita();
	
        	$datas = Vendedor::orderBy('id')->get();
		}

        return view('ventas.vendedor.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-vendedores');
        return view('ventas.vendedor.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionVendedor $request)
    {
        $vendedor = Vendedor::create($request->all());

		// Graba anita
		$Vendedor = new Vendedor();
        $Vendedor->guardarAnita($request, $vendedor->id);

        return redirect('ventas/vendedor')->with('mensaje', 'Vendedor creado con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-vendedores');
        $data = Vendedor::findOrFail($id);
        return view('ventas.vendedor.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionVendedor $request, $id)
    {
        can('actualizar-vendedores');
        Vendedor::findOrFail($id)->update($request->all());

		// Actualiza anita
		$Vendedor = new Vendedor();
        $Vendedor->actualizarAnita($request, $id);

        return redirect('ventas/vendedor')->with('mensaje', 'Vendedor actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-vendedores');

		// Elimina anita
		$Vendedor = new Vendedor();
        $Vendedor->eliminarAnita($id);

        if ($request->ajax()) {
            if (Vendedor::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
