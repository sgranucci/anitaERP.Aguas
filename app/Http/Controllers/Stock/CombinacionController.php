<?php

namespace App\Http\Controllers\Stock;

use App\Models\Stock\Combinacion;
use App\Models\Stock\Articulo;
use App\Models\Stock\Serigrafia;
use App\Models\Stock\Plvista;
use App\Models\Stock\Fondo;
use App\Models\Stock\Plarmado;
use App\Models\Stock\Color;
use App\Models\Stock\Horma;
use App\Models\Stock\Material;
use App\Models\Stock\Capeart;
use App\Models\Stock\Avioart;
use App\Http\Requests\ValidacionCombinacion;
use App\Http\Requests\ValidacionCombinacionTecnica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CombinacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function list()
    {
        $combinaciones = Combinacion::where("estado", "I")->count();
        $inactive = ( $combinaciones > 0 )?true:false;
        return view('stock.combinacion.index',compact('inactive'));
    }

    public function listJsonResponse($id = null)
    {
        if( $id ){
        	$combinacion = Combinacion::select('id','codigo','nombre','estado')->with('articulos:id,descripcion,sku');
            $combinacion->where("id",$id);
            $combinaciones = array('data' => $combinacion->get());
        }else{
        	$combinacion = Combinacion::select('id','codigo','nombre','estado')->with('articulos:id,descripcion,sku');
            $combinaciones = array('data' => $combinacion->get());
        }

        echo json_encode($combinaciones);
    }

    public function index($articulo_id = null)
    {
        $hay_combinacion = Combinacion::first();

        if( $articulo_id ){
        	$combinaciones = Combinacion::select('id','articulo_id','codigo','nombre','estado')->with('articulos:id,descripcion,sku')->
            				where("articulo_id",$articulo_id)->get();
        }else{
        	$combinaciones = Combinacion::select('id','articulo_id','codigo','nombre','estado')->with('articulos:id,descripcion,sku')->get();
        }

		if (!$hay_combinacion)
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

		if (!$hay_combinacion)
		{
        	if( $articulo_id ){
        		$combinaciones = Combinacion::select('id','articulo_id','codigo','nombre','estado')->with('articulos:id,descripcion,sku')->
            					where("articulo_id",$articulo_id)->get();
        	}else{
        		$combinaciones = Combinacion::select('id','articulo_id','codigo','nombre','estado')->with('articulos:id,descripcion,sku')->get();
        	}
		}

        return view('stock.combinacion.index',compact('combinaciones'));
    }

    public function create($id = null)
    {
        $stkmae = Articulo::where("id",$id)->first();
        return view('combinacion.diseno.create', compact('stkmae'));
    }

    public function save(ValidacionCombinacion $request)
    {
        $data = $request->all();
		
		// Graba anita
		$Combinacion = new Combinacion();
        $Combinacion->guardarAnita($request);

        $combinacion = Combinacion::create([
            'articulo_id' => $data['articulo_id'],
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'observacion' => $data['observacion'],
            'estado' => $data['estado'],
        ]);

        return redirect()->route('combinacion.index',$data['articulo_id'])->with('status', 'Combinación creada');   
    }

    public function edit($id, $type = null)
    {
        $combinacion = Combinacion::where("id",$id)->with("articulos:id,sku,descripcion")->with("capearts")->get()->first();

		$articulo = Articulo::all();

        if( $type == "tecnica" ){
			$plvista = Plvista::all();
			$fondo = Fondo::all();
	
			$plarmado = Plarmado::all();
			$color = Color::all();
			$horma = Horma::all();
			$serigrafia = Serigrafia::all();

			$capelladas = Articulo::select('id', 'sku', 'descripcion', 'usoarticulo_id')
                           ->where('usoarticulo_id','=',3)->get();
			$avios = Articulo::select('id', 'sku', 'descripcion', 'usoarticulo_id')
                           ->where('usoarticulo_id','=',6)->get();

        	$tipos = [
           	['id' => 'C', 'nombre'  => 'Capellada'],
           	['id' => 'B', 'nombre'  => 'Base'],
           	['id' => 'F', 'nombre'  => 'Forro'],
					];

        	$tipos_avios = [
           	['id' => 'A', 'nombre'  => 'Aplique'],
           	['id' => 'E', 'nombre'  => 'Empaque'],
					];
 
            return view('stock.combinacion.tecnica.edit',compact('combinacion','id','plvista','plarmado','fondo','color','horma','serigrafia','capelladas','avios','articulo','tipos','tipos_avios'));
        }else{
            return view('stock.combinacion.diseno.edit',compact('combinacion','id','articulo'));
        }
        
    }

    public function update(ValidacionCombinacion $request, $id)
    {
        $data = $request->all();

		// Actualiza anita
		$Combinacion = new Combinacion();
        $Combinacion->actualizarAnita($request, 'disenio');

        $combinacion = Combinacion::where('id', $data['id']);
        $combinacion->update([
            'articulo_id' => $data['articulo_id'],
            'codigo' => $data['codigo'],
            'nombre' => $data['nombre'],
            'observacion' => $data['observacion'],
            'estado' => $data['estado'],
        ]);

        return redirect()->route('combinacion.index',$data['articulo_id'])->with('status', 'Combinación editada');   
    }

    public function updateTecnica(ValidacionCombinacionTecnica $request)
	{
		// Actualiza anita
		$Combinacion = new Combinacion();
        $Combinacion->actualizarAnita($request, 'tecnica');

		// Graba combinacion
        $combinacion = Combinacion::where('id', $request->id);
        $combinacion->update([
                'plvista_id' => $request->plvista_id,
                'plarmado_id' => $request->plarmado_id,
                'fondo_id' => $request->fondo_id,
                'colorfondo_id' => $request->colorfondo_id,
                'horma_id' => $request->horma_id,
                'serigrafia_id' => $request->serigrafia_id,
            ]);

		// Graba capeart
		// Borra de anita todas las capelladas de la combinacion
		$Capeart = new Capeart();
        $Capeart->eliminarAnita($request->articulo_id, $request->codigo);

        $capeart = Capeart::where('combinacion_id', $request->combinacion_id)
                            ->where('articulo_id', $request->articulo_id)->delete();

        $materiales = $request->input('materiales', []);
        $colores = $request->input('colores', []);
        $piezas = $request->input('piezas', []);
        $consumo1 = $request->input('consumo1', []);
        $consumo2 = $request->input('consumo2', []);
        $consumo3 = $request->input('consumo3', []);
        $consumo4 = $request->input('consumo4', []);
        $tipos = $request->input('tipos', []);

        for ($i_capeart=0; $i_capeart < count($materiales); $i_capeart++) 
		{
            if ($materiales[$i_capeart] != '') 
			{
				// Graba anita
				$Capeart = new Capeart();
        		$Capeart->guardarAnita($request, $materiales[$i_capeart], $colores[$i_capeart], $piezas[$i_capeart],
					$consumo1[$i_capeart], $consumo2[$i_capeart], $consumo3[$i_capeart], $consumo4[$i_capeart], $tipos[$i_capeart], $i_capeart);

        		$capeart = Capeart::create([
                		'articulo_id' => $request->articulo_id, 
                		'combinacion_id' => $request->combinacion_id,
                		'material_id' => $materiales[$i_capeart],
                		'color_id' => $colores[$i_capeart],
                		'piezas' => $piezas[$i_capeart],
                		'consumo1' => $consumo1[$i_capeart],
                		'consumo2' => $consumo2[$i_capeart],
                		'consumo3' => $consumo3[$i_capeart],
                		'consumo4' => $consumo4[$i_capeart],
                		'tipo' => $tipos[$i_capeart],
            		]);
            }
        }

		// Graba avioart
		// Graba anita
		$Avioart = new Avioart();
        $Avioart->eliminarAnita($request->articulo_id, $request->codigo);
        
        $avioart = Avioart::where('combinacion_id', $request->combinacion_id)
                            ->where('articulo_id', $request->articulo_id)->delete();

        $materiales = $request->input('materiales_avios', []);
        $colores = $request->input('colores_avios', []);
        $consumo1 = $request->input('consumo1_avios', []);
        $consumo2 = $request->input('consumo2_avios', []);
        $consumo3 = $request->input('consumo3_avios', []);
        $consumo4 = $request->input('consumo4_avios', []);
        $tipos = $request->input('tipos_avios', []);

        for ($i_avioart=0; $i_avioart < count($materiales); $i_avioart++) 
		{
            if ($materiales[$i_avioart] != '') 
			{
				// Graba anita
				$Avioart = new Avioart();
        		$Avioart->guardarAnita($request, $materiales[$i_avioart], $colores[$i_avioart], 
					$consumo1[$i_avioart], $consumo2[$i_avioart], $consumo3[$i_avioart], $consumo4[$i_avioart], $tipos[$i_avioart], $i_avioart);

        		Avioart::create([
                		'articulo_id' => $request->articulo_id, 
                		'combinacion_id' => $request->combinacion_id,
                		'material_id' => $materiales[$i_avioart],
                		'color_id' => $colores[$i_avioart],
                		'consumo1' => $consumo1[$i_avioart],
                		'consumo2' => $consumo2[$i_avioart],
                		'consumo3' => $consumo3[$i_avioart],
                		'consumo4' => $consumo4[$i_avioart],
                		'tipo' => $tipos[$i_avioart],
            		]);
            }
        }

        return redirect()->route('combinacion.index',$request->articulo_id)->with('status', 'Combinación actualizada con exito');   
    }

    public function updateState(Request $request){
        $combinacion = Combinacion::where("id", $request["id"]);
        $combinacion->update([
            'estado' => $request["estado"]
        ]);

		// Actualiza anita
        $combinacion = Combinacion::where("id", $request["id"])->first();
		$Combinacion = new Combinacion();
        $Combinacion->actualizarAnita($combinacion, 'disenio');

        echo json_encode($request["estado"]);
    }

    public function updateStateAll(Request $request){
        $request = $request->all();
        $combinaciones = Combinacion::where("articulo_id", "<>" , NULL)->update(array("estado" => $request["estado"]));

		// Actualiza anita
		$Combinacion = new Combinacion();
        $Combinacion->actualizarAnita($request, 'disenio');

        return json_encode(["ok"]);
    }

    public function delete(Request $request){
        $request = $request->all();

		// Elimina anita
		$Combinacion = new Combinacion();
        $Combinacion->eliminarAnita($request->articulo_id, $request->codigo);

		$Capeart = new Capeart();
        $Capeart->eliminarAnita($request->articulo_id, $request->codigo);

		$Avioart = new Avioart();
        $Avioart->eliminarAnita($request->articulo_id, $request->codigo);

        $combinacion = Combinacion::where('codigo', $request["id"]);
        $combinacion->delete();
        return "";    
    }

}
