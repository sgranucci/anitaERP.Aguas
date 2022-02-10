<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Models\OC\OC_Product;
use App\Models\Contable\Cuentacontable;
use App\Models\Stock\Articulo;
use App\Models\Stock\Categoria;
use App\Models\Stock\Subcategoria;
use App\Models\Stock\Linea;
use App\Models\Stock\Unidadmedida;
use App\Models\Stock\Material;
use App\Models\Stock\Mventa;
use App\Models\Stock\Forro;
use App\Models\Stock\Fondo;
use App\Models\Stock\Compfondo;
use App\Models\Stock\Tipoarticulo;
use App\Models\Stock\Usoarticulo;
use App\Models\Stock\Tipocorte;
use App\Models\Stock\Puntera;
use App\Models\Stock\Contrafuerte;
use App\Models\Stock\Combinacion;
use App\Models\Stock\Capeart;
use App\Models\Stock\Avioart;
use App\Models\Configuracion\Impuesto;
use App\Http\Controllers\Controller;
use QrCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\ValidacionArticulo;
use App\Http\Requests\ValidacionArticuloTecnica;
use App\Http\Requests\ValidacionArticuloContaduria;
use App\ApiAnita;

class ArticuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request){

        $hay_articulos = Articulo::first();
		if (!$hay_articulos)
		{
        	$Articulo = new Articulo();
        	$Articulo->sincronizarConAnita();
		}

        $hay_combinaciones = Combinacion::first();
		if (!$hay_combinaciones)
		{
			$Combinacion = new Combinacion();
        	$Combinacion->sincronizarConAnita();
		}
        $hay_capeart = Capeart::first();
		if (!$hay_capeart)
		{
			$Capeart = new Capeart();
        	$Capeart->sincronizarConAnita();
		}
        $hay_avioart = Avioart::first();
		if (!$hay_avioart)
		{
			$Avioart = new Avioart();
        	$Avioart->sincronizarConAnita();
		}

        $combinaciones = Combinacion::where("estado", "I")->count();
        $inactive = ( $combinaciones > 0 )?true:false;
        $usosArticulos = Usoarticulo::all();

        $art_query = Articulo::select('articulo.id as id', 'sku as stkm_articulo', 'descripcion as stkm_desc', 'unidadmedida.nombre as stkm_unidad_medida', 'categoria.nombre as stkm_agrupacion', 'mventa.nombre as stkm_marca', 'linea.nombre as stkm_linea', 'usoarticulo_id')
                    ->leftJoin('categoria','articulo.categoria_id','=','categoria.id')
                    ->leftJoin('unidadmedida','articulo.unidadmedida_id','=','unidadmedida.id')
                    ->leftJoin('mventa','articulo.mventa_id','=','mventa.id')
                    ->leftJoin('linea','articulo.linea_id','=','linea.id');

		$filtros = [];
		if ($request->url() != $request->fullUrl())
		{
			$url = urldecode($request->fullUrl());
			$components = parse_url($url);
			parse_str($components['query'], $filtros);

			session(['filtros' => $filtros]);
		}
		else
		{
			$filtros = session('filtros');
		}

		// Aplica los filtros si es que hay definidos
		if ($filtros != '')
		{
			for ($ii = 1; $ii <= count($filtros['filter_column']); $ii++)
			{
				if ($filtros['filter_column'][$ii]['type'] == '')
					continue;

				if ($filtros['filter_column'][$ii]['column'] == 'estado' &&
					$filtros['filter_column'][$ii]['type'] == '=')
				{
					if ($filtros['filter_column'][$ii]['value'] == 'S')
					{
						$query = $art_query->whereNotExists(function($query)
							{
    							$query->select(DB::raw(1))
									->from("combinacion")
          							->whereRaw("combinacion.articulo_id=articulo.id");
							})->where('usoarticulo_id','=','1');
					}
					else
					{
						$estado = $filtros['filter_column'][$ii]['value'];
						$query = $art_query->whereExists(function($query) use($estado)
							{
    							$query->select(DB::raw(1))
									->from("combinacion")
          							->whereRaw("combinacion.articulo_id=articulo.id and combinacion.estado='".$estado."'");
							});
					}
				}
				else
				{
					switch($filtros['filter_column'][$ii]['type'])
					{
					case 'in':
						$query = $art_query->whereIn($filtros['filter_column'][$ii]['column'], explode(',', $filtros['filter_column'][$ii]['value']));
						break;
					case 'not in':
						$query = $art_query->whereNotIn($filtros['filter_column'][$ii]['column'], explode(',', $filtros['filter_column'][$ii]['value']));
						break;
					case 'like':
					case 'not like':
						$query = $art_query->where($filtros['filter_column'][$ii]['column'], $filtros['filter_column'][$ii]['type'], '%'.$filtros['filter_column'][$ii]['value'].'%');
						break;
					case '';
						$query = $art_query->whereExists(function($query)
								{
    								$query->select(DB::raw(1))
										->from("combinacion")
          								->whereRaw("combinacion.articulo_id=articulo.id and combinacion.estado='A'");
								});
						break;
					default:
						if ($filtros['filter_column'][$ii]['value'])
							$query = $art_query->where($filtros['filter_column'][$ii]['column'], $filtros['filter_column'][$ii]['type'], $filtros['filter_column'][$ii]['value']);
						break;
					}
				}

				if($filtros['filter_column'][$ii]['sorting'] != '')
				{
					$query = $art_query->orderBy('sku', $filtros['filter_column'][$ii]['sorting']);
				}
			}
		}
		else
		{
			$query = $art_query->whereExists(function($query)
					{
    					$query->select(DB::raw(1))
							->from("combinacion")
          					->whereRaw("combinacion.articulo_id=articulo.id and combinacion.estado='A'");
					});
		}

		$articulos = $art_query->get();

        return view("stock.product.list",compact('inactive', 'articulos', 'usosArticulos', 'filtros'));
    }

	public function limpiafiltro(Request $request) {
		session()->forget('filtros');

        return json_encode(["ok"]);
	}

	// Lista etiqueta QR

	public function download(Request $request, $sku, $codigo) {

        $articulo = Articulo::where("sku",$sku)->first();
        $combinacion = Combinacion::where("articulo_id",$articulo->id)->get();

		// Arma nombre de archivo
		$nombreEtiqueta = "tmp/eti-" . Str::random(10) . '.txt';

		$etiqueta = "";
		foreach($combinacion as $comb)
		{
			if (($codigo != "TODO" ? $comb->codigo == $codigo : $comb->estado == 'A'))
			{
			  	$qr = 'https://ferlimayoristas.com.ar/index.php?route=product/product&product_id=' . '1' . '0000' . 
					$articulo->sku . '0000' . $comb->codigo;

				$cod = substr($articulo->sku,0,-2);
				$sku = substr($articulo->sku,-2);
				$nombre1 = substr($comb->nombre,0,15);
				$nombre2 = substr($comb->nombre,15,15);

				if ($etiqueta == "")
					$etiqueta = "\nN\n";

				$etiqueta .= "q800\n";
				$etiqueta .= "A750,5,1,2,2,1,N,\"".$articulo->descripcion."\"\n";
				$etiqueta .= "A680,5,1,1,2,2,N,\"".$cod."-".$sku."\"\n";
				$etiqueta .= "A630,5,1,2,1,1,N,\"".$comb->codigo."-".$nombre1."\"\n";
				$etiqueta .= "A600,5,1,2,1,1,N,\"".$nombre2."\"\n";
				$etiqueta .= "b450,50,Q,s3eL,\"".$qr."\"\n";
				$etiqueta .= "A330,5,1,2,2,1,N,\"".$articulo->descripcion."\"\n";
				$etiqueta .= "A260,5,1,1,2,2,N,\"".$cod."-".$sku."\"\n";
				$etiqueta .= "A210,5,1,2,1,1,N,\"".$comb->codigo."-".$nombre1."\"\n";
				$etiqueta .= "A180,5,1,2,1,1,N,\"".$nombre2."\"\n";
				$etiqueta .= "b30,50,Q,s3eL,\"".$qr."\"\n";
				$etiqueta .= "P1\n";
			}
		}
		Storage::disk('local')->put($nombreEtiqueta, $etiqueta);
		$path = Storage::path($nombreEtiqueta);

		system("lp -dzebraarriba ".$path);

		Storage::disk('local')->delete($nombreEtiqueta);

        return redirect()->back()->with('status','El producto seleccionado no existe o no esta activo.');
    }

    public function create()
	{
        can('crear-articulos-disenio');

        $categoria = Categoria::orderBy('nombre')->get();
        $subcategoria = Subcategoria::orderBy('nombre')->get();
        $linea = Linea::where('nombre','!=','')->orderBy('nombre')->get();
        $marca = Mventa::orderBy('nombre')->get();
        $capellada = Material::orderBy('nombre')->get();
        $forro = Forro::orderBy('nombre')->get();
        $compfondo = Compfondo::orderBy('nombre')->get();
        $unidadmedida = Unidadmedida::orderBy('nombre')->get();
        $usosArticulos = Usoarticulo::all();

        return view("stock.product.diseno.create",compact('categoria','subcategoria','linea','marca','capellada','forro','compfondo','unidadmedida', 'usosArticulos'));
    }

    public function save(ValidacionArticulo $request)
	{
        can('crear-articulos-disenio');

		// Crea el articulo
        $articulo = Articulo::create($request->all());

		// Crea la Combinacion 1
        $combinacion = Combinacion::create([
		  	'articulo_id' => $articulo->id,
			'codigo' => '1',
            'nombre' => $articulo->descripcion,
            'observacion' => ' ',
            'estado' => 'A'
        ]);

        $producto = Articulo::with('categorias')
							->with('subcategorias')
							->with('lineas')
							->with('mventas')
							->with('impuestos')
							->with('unidadesdemedidas')
    						->with('unidadesdemedidasalternativas')
    						->with('cuentascontablesventas')
    						->with('cuentascontablescompras')
    						->with('cuentascontablesimpinternos')
    						->with('usoarticulos')
    						->with('materiales')
    						->with('tipocortes')
    						->with('punteras')
    						->with('contrafuertes')
    						->with('tipocorteforros')
    						->with('forros')
    						->with('compfondos')
							->where('id', $articulo->id)->get()->first();

		// Graba anita
		$Articulo = new Articulo();
        $Articulo->guardarAnita($producto);

        return redirect()->route('products.index')->with('status', 'Producto creado');
    }

    public function edit($id, $type = null, $filtros = null){

        $producto = Articulo::with('categorias')
							->with('subcategorias')
							->with('lineas')
							->with('mventas')
							->with('impuestos')
							->with('unidadesdemedidas')
    						->with('unidadesdemedidasalternativas')
    						->with('cuentascontablesventas')
    						->with('cuentascontablescompras')
    						->with('cuentascontablesimpinternos')
    						->with('usoarticulos')
    						->with('materiales')
    						->with('tipocortes')
    						->with('punteras')
    						->with('contrafuertes')
    						->with('tipocorteforros')
    						->with('forros')
    						->with('compfondos')
							->where('id', $id)->get()->first();

        $categoria = Categoria::orderBy('nombre')->get();
        $subcategoria = Subcategoria::orderBy('nombre')->get();
        $unidadmedida = Unidadmedida::orderBy('nombre')->get();
        $marca = Mventa::orderBy('nombre')->get();
        $linea = Linea::orderBy('nombre')->get();
        $forro = Forro::orderBy('nombre')->get();
        $compfondo = Compfondo::orderBy('nombre')->get();
        $capellada = Material::orderBy('nombre')->get();
        $usosArticulos = Usoarticulo::get();
        $tipoCorte = Tipocorte::orderBy('nombre')->get();
        $punteras = Puntera::orderBy('nombre')->get();
        $contrafuertes = Contrafuerte::orderBy('nombre')->get();

        if( $type == "tecnica" ){
            return view('stock.product.tecnica.edit',compact('producto','id', 'categoria', 'marca','linea','compfondo','forro','usosArticulos','tipoCorte','punteras','capellada','unidadmedida','contrafuertes','filtros'));
        }elseif( $type == "contaduria" ){
                
        		$ctamae = Cuentacontable::orderBy('codigo')->get();
        		$codimp = Impuesto::all();
                
                return view('stock.product.contaduria.edit',compact('producto','id', 'categoria', 'marca','linea','compfondo','forro','usosArticulos','tipoCorte','punteras','ctamae','codimp','capellada','unidadmedida','filtros'));
        }else{

            return view('stock.product.diseno.edit',compact('producto','id', 'categoria', 'subcategoria', 'marca','linea','compfondo','forro','usosArticulos','tipoCorte','punteras','capellada','unidadmedida','filtros'));    
        }
        
    }

    public function actualizar(ValidacionArticulo $request, $id)
    {
        can('actualizar-articulos-disenio');

        Articulo::findOrFail($request->id)->update($request->all());

		// Lee nuevo precio con relaciones para interface Anita
        $producto = Articulo::with('categorias')->with('subcategorias')->with('lineas')->with('mventas')->with('impuestos')
							->with('unidadesdemedidas')->with('unidadesdemedidasalternativas')->with('cuentascontablesventas')
    						->with('cuentascontablescompras')->with('cuentascontablesimpinternos')->with('usoarticulos')
    						->with('materiales')->with('tipocortes')->with('punteras')->with('contrafuertes') 
							->with('tipocorteforros')->with('forros')->with('compfondos')->where('id', $request->id)->
							get()->first();

		// Actualiza anita
		$Articulo = new Articulo();
        $Articulo->actualizarAnita($producto, $producto->sku);

        return redirect('stock/products')->with('status', 'Articulo actualizado con exito');
    }

    public function updateTecnica(ValidacionArticuloTecnica $request, $id)
	{
        can('actualizar-articulos-tecnica');

        Articulo::findOrFail($request->id)->update($request->all());

		// Lee nuevo precio con relaciones para interface Anita
        $producto = Articulo::with('categorias')->with('subcategorias')->with('lineas')->with('mventas')->with('impuestos')
							->with('unidadesdemedidas')->with('unidadesdemedidasalternativas')->with('cuentascontablesventas')
    						->with('cuentascontablescompras')->with('cuentascontablesimpinternos')->with('usoarticulos')
    						->with('materiales')->with('tipocortes')->with('punteras')->with('contrafuertes') 
							->with('tipocorteforros')->with('forros')->with('compfondos')->where('id', $request->id)->
							get()->first();

		// Actualiza anita
		$Articulo = new Articulo();
        $Articulo->actualizarAnita($producto, $producto->sku);

        return redirect('stock/products')->with('status', 'Articulo actualizado con exito');
    }

    public function updateContaduria(ValidacionArticuloContaduria $request, $id)
	{
        can('actualizar-articulos-contaduria');

        Articulo::findOrFail($request->id)->update($request->all());

		// Lee nuevo precio con relaciones para interface Anita
        $producto = Articulo::with('categorias')->with('subcategorias')->with('lineas')->with('mventas')->with('impuestos')
							->with('unidadesdemedidas')->with('unidadesdemedidasalternativas')->with('cuentascontablesventas')
    						->with('cuentascontablescompras')->with('cuentascontablesimpinternos')->with('usoarticulos')
    						->with('materiales')->with('tipocortes')->with('punteras')->with('contrafuertes') 
							->with('tipocorteforros')->with('forros')->with('compfondos')->where('id', $request->id)->get()->first();

		// Actualiza anita
		$Articulo = new Articulo();
        $Articulo->actualizarAnita($producto, $producto->sku);

        return redirect('stock/products')->with('status', 'Articulo actualizado con exito');
    }

    public function delete(Request $request, $id){
        can('borrar-articulos');

        $producto = Articulo::select('sku')->where('id', $id)->first();

		// Elimina anita
		$Articulo = new Articulo();
        $Articulo->eliminarAnita($producto->sku);

        if ($request->ajax()) {
            if (Articulo::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}

