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
use PDF;
use QrCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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

		// Aplica los filtros si es que hay definidos
		$filtros = [];
		if ($request->url() != $request->fullUrl())
		{
			$url = urldecode($request->fullUrl());
			$components = parse_url($url);
			parse_str($components['query'], $filtros);
			$_where = "";
			for ($ii = 1; $ii <= count($filtros['filter_column']); $ii++)
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
          							->whereRaw("combinacion.articulo_id=articulo.id");
							});
					break;
				default:
					$query = $art_query->where($filtros['filter_column'][$ii]['column'], $filtros['filter_column'][$ii]['type'], $filtros['filter_column'][$ii]['value']);
					break;
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
          					->whereRaw("combinacion.articulo_id=articulo.id");
					});
		}

		$articulos = $art_query->get();

        return view("stock.product.list",compact('inactive', 'articulos', 'usosArticulos', 'filtros'));
    }

	public function download(Request $request) {
	  	$id = $request->id;
        $product = Articulo::where("id",$id)->first();
        if( $product ){
            $url = 'https://ferlimayoristas.com.ar/index.php?route=product/product&product_id=' . '1' . '0000' . $request->sku . '0000' . $request->codigo;
            $pdf    = PDF::setOptions(['isHtml5ParserEnabled' => true,
                              'isRemoteEnabled' => true])
                              ->loadView('pdf/index',['url' => $url]);
          $pdf->getDomPDF()->set_option("enable_php", true);
          return $pdf->download($id . '.pdf');
        }else{
            return redirect()->route('combinacion.index')->with('status','El producto seleccionado no existe o no esta activo.');
        }
    }

    public function create()
	{
        can('crear-articulos-disenio');

        $categoria = Categoria::all();
        $subcategoria = Subcategoria::all();
        $linea = Linea::where('nombre','!=','')->orderBy('nombre')->get();
        $marca = Mventa::all();
        $capellada = Material::all();
        $forro = Forro::all();
        $compfondo = Compfondo::all();
        $unidadmedida = Unidadmedida::all();
        $usosArticulos = Usoarticulo::all();

        return view("stock.product.diseno.create",compact('categoria','subcategoria','linea','marca','capellada','forro','compfondo','unidadmedida', 'usosArticulos'));
    }

    public function save(ValidacionArticulo $request)
	{
        can('crear-articulos-disenio');

		// Crea el articulo
        $articulo = Articulo::create($request->all());

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

        $categoria = Categoria::all();
        $subcategoria = Subcategoria::all();
        $unidadmedida = Unidadmedida::all();
        $marca = Mventa::all();
        $linea = Linea::all();
        $forro = Forro::all();
        $compfondo = Compfondo::all();
        $capellada = Material::all();
        $usosArticulos = Usoarticulo::all();
        $tipoCorte = Tipocorte::all();
        $punteras = Puntera::all();
        $contrafuertes = Contrafuerte::all();

        if( $type == "tecnica" ){
            return view('stock.product.tecnica.edit',compact('producto','id', 'categoria', 'marca','linea','compfondo','forro','usosArticulos','tipoCorte','punteras','capellada','unidadmedida','contrafuertes','filtros'));
        }elseif( $type == "contaduria" ){
                
        		$ctamae = Cuentacontable::all();
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

        $producto = Articulo::select('sku')->where('id', $id)->get()->first();

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

