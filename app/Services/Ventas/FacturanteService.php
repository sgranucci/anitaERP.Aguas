<?php
namespace App\Services\Ventas;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Queries\Stock\ArticuloQueryInterface;
use App\Models\Stock\Combinacion;
use App\Models\Stock\Categoria;
use App\Models\Stock\Talle;
use App\ApiAnita;
use Exception;
use SoapClient;
use Log;

class FacturanteService 
{
	var $client;
	private $facturacionService;
	protected $articuloQuery;
	
    public function __construct(FacturacionService $facturacionservice,
								ArticuloQueryInterface $articuloquery
								)
    {
		$this->facturacionService = $facturacionservice;
		$this->articuloQuery = $articuloquery;
    }

	public function listadoComprobanteFull($params) 
	{
		//Prueba
		//$auth = array(
		//	"Empresa" => 3430,
		//	"Hash" => "test",
		//	"Usuario" => "pruebalistar"
		//);

		// Prueba Ferli
		$auth = array(
			"Empresa" => 4102,
			"Hash" => "c7iA03YLl8lg",
			"Usuario" => "calzadosferlitest@yopmail.com"
		);

		$parametros = array(
					'Autenticacion' => $auth,
					'FechaDesde' => $params['desdefecha'],
					'FechaHasta' => $params['hastafecha'],
					'NroPagina' => 1
		);

		$request = array("request" => $parametros);

		$this->client = $this->_client();
		try {
		  $result = $this->client->ListadoComprobantesFull($request);
		  return($result->ListadoComprobantesFullResult->ListadoComprobantes->Comprobante);
		}
		catch (\Exception $e) {
		 	Log::info('Caught Exception :'. $e->getMessage());
			return $e;       // just re-throw it
		}
	}

	private function _client() 
	{
		$wsdl = "http://testing.facturante.com/api/Comprobantes.svc?wsdl";
		try {
		  $this->client = new \SoapClient($wsdl);
		return $this->client;
		}
		catch ( \Exception $e) {
		  Log::info('Caught Exception in client'. $e->getMessage());
		}
	}

	public function generaFactura($datos)
	{
		// Graba anita
		$puntoVenta = intval($datos->Prefijo);
		$letra = substr($datos->TipoComprobante, -1);

		switch($datos->TipoComprobante)
		{
			case 'FA':
			case 'FB':
			case 'FC':
				$tipoComprobante = 'FAC';
				$signo = 1.;
				break;
			case 'NCA':
			case 'NCB':
			case 'NCC':
				$tipoComprobante = 'NCD';
				$signo = -1.;
				break;
			case 'NDA':
			case 'NDB':
			case 'NDC':
				$tipoComprobante = 'NDB';
				$signo = 1.;
				break;
		}
		$condicionVenta_Id = 3;
		switch($datos->CondicionVenta)
		{
			case 1:
				$condicionVenta_Id = 3;
				break;
			default:
				$condicionVenta_Id = 101;
				break;
		}
		// Arma tabla venta
		$venta = [
					'codigo' => $tipoComprobante,
					'numerocomprobante' => $datos->Numero,
					'fecha' => $datos->FechaHora,
					'fechajornada' => $datos->FechaHora,
					'total' => $datos->Total,
					'moneda_id' => 1,
					'condicionventa_id' => $condicionVenta_Id,
					'lugarentrega' => $datos->Cliente->DireccionFiscal,
					'nombrecliente' => $datos->Cliente->RazonSocial,
					'documentocliente' => $datos->Cliente->NroDocumento,
					'transporte_id' => 0,
					'descuentointegrado' => '',
					'cliente_id' => 0
		];

		$dataCAE = [
					'gravado' => floatval($datos->TotalNeto),
					'iva' => floatval($datos->IVA2)+floatval($datos->IVA1),
					'total' => floatval($datos->Total),
					'nogravado' => floatval($datos->SubtotalNoAlcanzado),
					'exento' => floatval($datos->SubTotalExcento)
		];

		$conceptosTotales = [];
		$cuentacorriente = [];
		
		if (floatval($datos->PercepcionIIBB) != 0)
		{
			$tasa = 0;
			if (floatval($datos->TotalNeto) != 0)
				$tasa = floatval($datos->PercepcionIIBB) / floatval($datos->TotalNeto);

			$conceptosTotales[] = [
					'concepto' => "Percepcion IIBB",
					'jurisdiccion' => "902",
					'provincia_id' => 2,
					'tasa' => $tasa,
					'importe' => floatval($datos->PercepcionIIBB)
			];
		}
		if (floatval($datos->IVA2) != 0)
		{
			$conceptosTotales[] = [
				'concepto' => "Total Iva",
				'tasa' => 21,
				'importe' => floatval($datos->IVA2)
			];			
		}
		if (floatval($datos->IVA1) != 0)
		{
			$conceptosTotales[] = [
				'concepto' => "IVA",
				'tasa' => 10.5,
				'importe' => floatval($datos->IVA1)
			];			
		}
		$dataFactura = [];
		foreach ($datos->Items->ComprobanteItem as $item)
		{
			if ($item->Detalle != "Descuentos y promociones")
			{
				$impuesto_id = 3;
				switch(floatval($item->IVA))
				{
					case 0:
						$impuesto_id = 2;
						break;
					case 10.5:
						$impuesto_id = 2;
						break;
					case 21:
						$impuesto_id = 3;
						break;					
				} 

				// Tiene que buscar el articulo en base al SKU
				$codigo = explode("-", $item->Codigo);
				$sku = $codigo[0];
				$codigoCombinacion = $codigo[1];
				$talle = $codigo[2];

				// Busca el articulo
				$articulo = $this->articuloQuery->traeArticuloPorSku($sku);
				$combinacion_id = $talle_id = 0;
				$codigoCategoria = "";
				$talle_nombre = "";
				if ($articulo)
				{
					// Trae la categoria
					$categoria = Categoria::find($articulo->categoria_id);
					if ($categoria)
						$codigoCategoria = $categoria->codigo;
					
					$combinacion = Combinacion::where('articulo_id', $articulo->id)
										->where('codigo', $codigoCombinacion)->first();
					if ($combinacion)
						$combinacion_id = $combinacion->id;

					$talle = Talle::where('nombre', $talle)->first();

					if ($talle)
					{
						$talle_id = $talle->id;
						$talle_nombre = $talle->nombre;
					}
				}

				$medida = [];
				$medida[] = [
					'id' => 1,
					'talle' => $talle_id,
					'medida' => $talle_nombre,
					'cantidad' => floatval($item->Cantidad),
					'precio' => floatval($item->PrecioUnitario),
					'pedido' => ''
				];

				$dataFactura[] = ["cantidad" => floatval($item->Cantidad),
					"precio" => floatval($item->PrecioUnitario),
					"descuento" => floatval($item->Bonificacion),
					"descuentointegrado" => '',
					"descuentofinal" => 0,
					"descuentointegradofinal" => '',
					"incluyeimpuesto" => '1',
					"impuesto_id" => $impuesto_id,
					"articulo_id" => $articulo->id,
					"sku" => $sku,
					"descripcion" => $item->Detalle,
					"codigounidadmedida" => 1,
					'categoria' => $codigoCategoria,
					"combinacion_id" => $combinacion_id,
					'codigocombinacion' => $codigoCombinacion,
					'modulo_id' => 30,
					'moneda_id' => 1,
					'listaprecio_id' => 1,
					'despacho' => '',
					'loteimportacion_id' => 0,
					'ordentrabajo_id' => 0,
					'pedido_combinacion_id' => 0,
					'medidas' => $medida
				];
			}
		}
		$cuentaVenta = '411000003';
		$contrapartida = '113100007';

		$cae['cae'] = $datos->CAE;
		$cae['fechavencimientocae'] = $datos->FechaVencimientoCae;
		//try {
			$anita = $this->facturacionService->grabaAnita($puntoVenta, $letra, 0, 0,
							$venta, $dataCAE, $conceptosTotales, $cuentacorriente, $dataFactura, $signo, 
							$cuentaVenta, $contrapartida);

			//if ($anita == 'Error')
			//	throw new Exception('Error en grabacion anita.');

			//if ($anita == 'Errvend')
			//	throw new Exception('No tiene vendedor asignado.');

			$vencae = $this->facturacionService->grabaVenCae(substr($venta['codigo'], 0, 3), $letra, $puntoVenta, 
				$venta['numerocomprobante'], $cae['cae'], date('Ymd', strtotime($cae['fechavencimientocae'])));
		//}
		//catch ( \Exception $e) {

			//Log::info('Error al generar factura TiendaNube '. $e->getMessage());

			// Borra factura de anita
			//if ($venta['codigo'] ?? '')
			//	$this->facturacionService->borraAnita(substr($venta['codigo'], 0, 3), $letra, 
			//					$puntoVenta, $venta['numerocomprobante']);
		//}
	}
}
