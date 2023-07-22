<?php
namespace App\Services\Stock;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Queries\Stock\ArticuloQueryInterface;
use App\Models\Stock\Articulo;
use App\Models\Stock\Combinacion;
use App\Models\Stock\Precio;
use App\Models\Stock\Listaprecio;
use App\Models\Stock\Talle;
use App\Models\Stock\Linea;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PrecioService 
{
	public function __construct(
								)
    {
    }

	public function asignaListaPrecio($talle, $tiponumeracion_id)
	{
		$listaprecio = Listaprecio::all();

		$lista_id = 0;
		foreach($listaprecio as $lista)
		{
			if ($talle >= $lista->desdetalle && $talle <= $lista->hastatalle &&
				$tiponumeracion_id == $lista->tiponumeracion_id)
			{
				$lista_id = $lista->id;
				break;
			}
		}
		return $lista_id;
	}

	public function asignaPrecio($articulo_id, $talle_id, $fechavigencia)
	{
		$talle_id = preg_replace('([^A-Za-z0-9,])', '', $talle_id);
		$array_talle = explode(',', $talle_id);
		$array_precio = [];
		if ($talle_id)
		{
			$talle = Talle::select('nombre', 'id')->whereIn('id', $array_talle)->get();
			
			// Lee el articulo
			$articulo = Articulo::select('linea_id')->where('id', $articulo_id)->first();

			$tiponumeracion_id = 0;
			if ($articulo)
			{
				$linea = Linea::find($articulo->linea_id);

				if ($linea)
					$tiponumeracion_id = $linea->tiponumeracion_id;
			}
	
			foreach($talle as $value)
			{
				$lista = $this->asignaListaPrecio($value->nombre, $tiponumeracion_id);

				if (gettype($fechavigencia) == "string")
					$fecha = $fechavigencia;
				else
					$fecha = date('Y-m-d', strtotime($fechavigencia));
			
				$precio = Precio::with('listaprecios')
								->where('articulo_id',$articulo_id)
								->where('listaprecio_id',$lista)
								->where('fechavigencia', '<=', $fecha)
								->orderBy('fechavigencia', 'desc')
								->first();

				if ($precio)
				{
					$precio_talle = $precio->precio;
					$listaprecio_id = $precio->listaprecio_id;
					$moneda_id = $precio->moneda_id;
					$incluyeimpuesto = $precio->listaprecios->incluyeimpuesto;
				}
				else
				{
					$precio_talle = 0;
					$listaprecio_id = 0;
					$moneda_id = 1;
					$incluyeimpuesto = 1;
				}

				$array_precio[] = [
									'precio'=>$precio_talle,
				  					'listaprecio_id'=>$listaprecio_id,
				  					'moneda_id'=>$moneda_id,
				  					'incluyeimpuesto'=>$incluyeimpuesto,
				  					];
			}
		}
		return($array_precio);
	}

	public function asignaPrecioPorTipoNumeracion($articulo_id, $tiponumeracion_id, $fechavigencia)
	{	
		$listaprecio = Listaprecio::all();

		$array_precio = [];
		foreach($listaprecio as $lista)
		{
			if ($tiponumeracion_id == $lista->tiponumeracion_id)
			{
				$lista_id = $lista->id;
				
				$precio = Precio::with('listaprecios')
								->where('articulo_id',$articulo_id)
								->where('listaprecio_id',$lista_id)
								->whereDate('fechavigencia', '<=', date('Y-m-d', strtotime($fechavigencia)))
								->orderBy('fechavigencia', 'desc')
								->first();
	
				if ($precio)
				{
					$precio_talle = $precio->precio;
					$listaprecio_id = $precio->listaprecio_id;
					$moneda_id = $precio->moneda_id;
					$incluyeimpuesto = $precio->listaprecios->incluyeimpuesto;
				}
				else
				{
					$precio_talle = 0;
					$listaprecio_id = 0;
					$moneda_id = 1;
					$incluyeimpuesto = 1;
				}

				$array_precio[] = [
					'precio'=>$precio_talle,
					'listaprecio_id'=>$listaprecio_id,
					'moneda_id'=>$moneda_id,
					'incluyeimpuesto'=>$incluyeimpuesto,
					];
			}
		}
		return($array_precio);
	}

	public static function asignaPrecioPorLista($articulo_id, $listaprecio_id, $fechavigencia)
	{
		// Asigna precio por vigencia
		$precios = Precio::select('articulo_id', 'listaprecio_id', 'fechavigencia', 'precio')
						->where('articulo_id', '=', $articulo_id)
						->where('listaprecio_id', '=', $listaprecio_id)
						->orderBy('fechavigencia')
						->get();

		$precioRet = 0;
		foreach($precios as $precio)
		{
			if ($precio->fechavigencia <= $fechavigencia)
				$precioRet = $precio->precio;
		}

		return($precioRet);
	}

}

