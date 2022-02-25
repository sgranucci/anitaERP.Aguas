<?php
namespace App\Services\Ventas;

use App\Queries\Ventas\OrdentrabajoQueryInterface;
use App\Models\Stock\Articulo;
use App\Models\Stock\Combinacion;
use App\Models\Stock\Linea;
use App\Models\Stock\Forro;
use App\Models\Stock\Material;
use App\Models\Configuracion\Empresa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App;
use Auth;

class OrdentrabajoService 
{
	protected $ordentrabajoRepository;
	protected $ordentrabajoQuery;

    public function __construct(
								OrdentrabajoQueryInterface $ordentrabajoquery
								)
    {
        $this->ordentrabajoQuery = $ordentrabajoquery;
    }

	public function leeOrdenestrabajoPendientes()
	{
	  	return $this->ordentrabajoQuery->allOrdentrabajoPorEstado('P');
	}

	public function listaEtiquetaOT(array $data)
	{
		// Arma nombre de archivo
		$nombreEtiqueta = "tmp/etiOT-" . Str::random(10) . '.txt';

		$etiqueta = "";
		$pos = [44,66,88,110,132,154];
		foreach($data['ordenestrabajo'] as $id)
		{
    		$ot = $this->ordentrabajoQuery->traeOrdentrabajoPorId($id);

			foreach($ot as $item)
			{
				// Lee articulo
			 	$articulo = Articulo::where('sku', ltrim($item->ordtv_articulo, '0'))->first();

				$buff = [];
				if ($articulo)
				{
				  	$combinacion = Combinacion::where('articulo_id', $articulo->id)
											->where('codigo', $item->ordtm_capellada)
											->first();

					if ($combinacion)
					{
					  	$empresa = Empresa::where('codigo',1)->first();

						if ($empresa)
						{
						  	$buff[] = $empresa->nombre;
							$buff[] = "C.U.I.T. ".$empresa->nroinscripcion;
						}
						else
						{
						  	$buff[] = "EMPRESA";
						  	$buff[] = "CUIT";
						}

						$material = Material::findorFail($articulo->material_id);
						if ($material)
						{
						  	if ($material->codigo == 3 || $material->codigo == 4)
							{
							  	$hoy = $date = Carbon::now();
							  	$vto = $hoy->addYears(2);
								$vto = $vto->format('d-m-Y');

						  		$buff[] = "CAPELLADA ".$material->nombre." - Vto.: ".$vto;
							}
							else
						  		$buff[] = "CAPELLADA ".$material->nombre;
						}

						$linea = Linea::find($articulo->linea_id);
						$forro = Forro::find($articulo->forro_id);
						if ($linea && $forro)
						  	$buff[] = "FONDO ".$linea->nombre." FORRO ".substr($forro->nombre,0,6);

						$buff[] = "ARTICULO ".$articulo->sku;
						$buff[] = "FERLI (MR)-MADE IN ARGENTINA";
					}
				}

				if ($etiqueta == "")
					$etiqueta = "\nN\n";

				for ($i = 0; $i < count($buff); $i++)
            		$etiqueta .= "A30,".$pos[$i].",0,1,1,2,N,\"".$buff[$i]."\"\n";

				$etiqueta .= "P1\n";
			}
		}
		Storage::disk('local')->put($nombreEtiqueta, $etiqueta);
		$path = Storage::path($nombreEtiqueta);

		system("lp -dzebraarriba ".$path);

		Storage::disk('local')->delete($nombreEtiqueta);

        return redirect()->back()->with('status','Las ordenes seleccionadas no existen');
    }
}
