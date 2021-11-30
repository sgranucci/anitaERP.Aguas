<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Stock\Precio;
use Illuminate\Support\Facades\Storage;
use App\Models\Seguridad\Usuario;
use App\Models\Stock\Articulo;
use App\Models\Stock\Listaprecio;
use App\Models\Configuracion\Moneda;
use App\Http\Requests\ValidacionPrecio;
use Carbon\Carbon;
use Auth;

class PrecioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-precios');
        $datas = Precio::with('articulos')->with('listaprecios')->with('monedas')->with('usuarios')->take(5000)->get();

		if ($datas->isEmpty())
		{
			$Precio = new Precio();
        	$Precio->sincronizarConAnita();
	
        	$datas = Precio::with('articulos')->with('listaprecios')->with('moneda')->with('usuario')->take(5000)->get();
		}

        return view('stock.precio.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-precios');
		$articulo_query = Articulo::all();
		$listaprecio_query = Listaprecio::all();
		$moneda_query = Moneda::all();

        return view('stock.precio.crear', compact('articulo_query', 'listaprecio_query', 'moneda_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionPrecio $request)
    {
		$fechavigencia = Carbon::createFromFormat('d-m-Y', $request->fechavigencia);

		$precio = Precio::create([
			"articulo_id" => $request->articulo_id,
			"listaprecio_id" => $request->listaprecio_id,
			"fechavigencia" => $fechavigencia,
			"moneda_id" => $request->moneda_id,
			"precio" => $request->precio,
			"precioanterior" => 0,
			"usuarioultcambio_id" => Auth::user()->id,
				]);

		// Lee nuevo precio con relaciones para interface Anita
        $precio = Precio::where('id', $precio->id)->with('articulos:id,descripcion,sku')->with('listaprecios')->with('monedas')->with('usuarios')->first();

		// Graba anita
		$Precio = new Precio();
        $Precio->guardarAnita($precio);

        return redirect('stock/precio')->with('mensaje', 'Precio creado con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-precios');

        $precio = Precio::where('id', $id)->with('articulos:id,descripcion,sku')->with('listaprecios')->with('monedas')->with('usuarios')->first();
		$articulo_query = Articulo::select('id', 'sku', 'descripcion')->where('sku', 'not like', '%FON%')->where('sku', 'not like', '%SER%')->orderby('descripcion')->get();
		$listaprecio_query = Listaprecio::all();
		$moneda_query = Moneda::all();

        return view('stock.precio.editar', compact('precio', 'articulo_query', 'listaprecio_query', 'moneda_query'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionPrecio $request, $id)
    {
        can('actualizar-precios');

		// Lee precio anterior
        $precio_ant = Precio::select('precio')->where('id', $id)->first();
		$fechavigencia = Carbon::createFromFormat('d-m-Y', $request->fechavigencia);

        $precio = Precio::where('id',$id)->findOrFail($id)
                ->update([
					'articulo_id' => $request->articulo_id,
					'listaprecio_id' => $request->listaprecio_id,
					'fechavigencia' => $fechavigencia,
					'moneda_id' => $request->moneda_id,
					'precio' => $request->precio,
					'precioanterior' => $precio_ant->precio,
					'usuarioultcambio_id' => Auth::user()->id
					]);

		// Lee nuevo precio con relaciones para interface Anita
        $precio = Precio::where('id', $id)->with('articulos:id,descripcion,sku')->with('listaprecios')->with('monedas')->with('usuarios')->first();

		// Actualiza anita
		$Precio = new Precio();
        $Precio->actualizarAnita($precio);

        return redirect('stock/precio')->with('mensaje', 'Precio actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-precios');

        $precio = Precio::where('id', $id)->with('articulos:id,descripcion,sku')->with('listaprecios')->with('monedas')->with('usuarios')->first();

		// Elimina anita
		$Precio = new Precio();
        $Precio->eliminarAnita($precio->articulos->codigo, $precio->listaprecio->codigo);

        if ($request->ajax()) {
            if (Precio::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
