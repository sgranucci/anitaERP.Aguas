<?php
namespace App\Services\Ventas;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Configuracion\Empresa;
use App\Services\Configuracion\ImpuestoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FacturaElectronicaService 
{
	public function __construct()
    {
        
    }

	public function traeUltimoNumeroComprobante($nroinscripcion, $tipotransaccion, $puntoventa)
	{
		$req['solicitud']['servicio'] = 'wsfe_v1';
		$req['solicitud']['funcion'] = 'SolicitarUltimoCompEnviado';
		$req['datos']['cuit']   = (double) str_replace("-","",$nroinscripcion);
        $req['datos']['pventa'] = (int) $puntoventa;
        $req['datos']['tipo']   = (int) $tipotransaccion;

		$base = "ultnrocomp-". Str::random(20);
		$nombreXml = $base.'.xml';
		$nombreXmlRespuesta = $base.'_RESP.xml';
		Storage::disk('public')->put("afip/ent/$nombreXml", $this->GenerarXML ($req, null));

		$ret = $this->ejecutaAfip($nombreXml);

		Storage::disk('public')->delete("afip/ent/$nombreXml");

		$resp = new \SimpleXMLElement("storage/afip/ent/$nombreXmlRespuesta", null, true);

		$mensaje = $resp->mensaje;
		$tipo = $resp->detalle->tipo;
		$pventa = $resp->detalle->pventa;
		$ultimoNumero = -1;

		if ($tipo == $tipotransaccion && $pventa == $puntoventa)
			$ultimoNumero = strval($resp->detalle->numero);
		
		Storage::disk('public')->delete("afip/ent/$nombreXmlRespuesta");

		return $ultimoNumero;
	}

	public function solicitaCAE($nroinscripcion, $tipotransaccion, $puntoventa, $datos)
	{
		$req['solicitud']['servicio'] = 'wsfe_v1';
		$req['solicitud']['funcion'] = 'SolicitarCAE';
		$req['cabecera']['cuit']   = (double) str_replace("-","",$nroinscripcion);
        $req['cabecera']['pto_venta'] = (int) $puntoventa;
        $req['cabecera']['tipo_cbt']   = (int) $tipotransaccion;

		$req['detalle']['concepto'] = 1;

		$req['detalle']['doc_tipo'] = $datos['tipodoc'];
		$req['detalle']['doc_nro'] = str_replace("-","",$datos['nroinscripcion']);
		$req['detalle']['cbt_desde'] = $datos['numerocomprobante'];
		$req['detalle']['cbt_hasta'] = $datos['numerocomprobante'];
		$req['detalle']['fecha_cbt'] = $datos['fechacomprobante'];
		$req['detalle']['importe_total'] = Round($datos['total'],2);
		$req['detalle']['importe_total_conc'] = Round($datos['nogravado'],2);
		$req['detalle']['importe_neto'] = Round($datos['gravado'],2);
		$req['detalle']['importe_op_ex'] = Round($datos['exento'],2);
		$req['detalle']['importe_iva'] = Round($datos['iva'],2);
		$req['detalle']['importe_trib'] = Round($datos['tributo'],2);
		$req['detalle']['fecha_serv_desde'] = '';
		$req['detalle']['fecha_serv_hasta'] = '';
		$req['detalle']['fecha_vto_pago'] = '';
		$req['detalle']['moneda_id'] = $datos['moneda'];
		$req['detalle']['moneda_cotizac'] = $datos['cotizacion'];

		if ($datos['fechaasignaciondesde'] > 0 && count($datos['comprobantesasociados']) == 0 &&
			($tipotransaccion == 3 || $tipotransaccion == 8 || $tipotransaccion == 203))
		{
			$req['detalle']['fchdesde'] = $datos['fechaasignaciondesde'];
			$req['detalle']['fchhasta'] = $datos['fechaasignacionhasta'];
		}

		// Comprobantes asociados
		foreach($datos['comprobantesasociados'] as $asociado)
		{
			$req['detalle']['cbteasoc']['tipo'] = $asociado['tipo'];
			$req['detalle']['cbteasoc']['ptovta'] = $asociado['ptovta'];
			$req['detalle']['cbteasoc']['nro'] = $asociado['nro'];
		}

		// Tributos
		if (count($datos['tributos']) > 1)
		{
			for ($j = 0; $j < count($datos['tributos']); $j++)
			{
				if ($datos['tributos'][$j]['importe'] != 0)
				{
					$req['detalle']['tributos']['tributo'][$j]['id'] = $datos['tributos'][$j]['id'];
					$req['detalle']['tributos']['tributo'][$j]['base_imp'] = Round($datos['tributos'][$j]['base_imp'],2);
					$req['detalle']['tributos']['tributo'][$j]['importe'] = Round($datos['tributos'][$j]['importe'],2);
					$req['detalle']['tributos']['tributo'][$j]['alicuota'] = $datos['tributos'][$j]['alicuota'];
					$req['detalle']['tributos']['tributo'][$j]['desc'] = $datos['tributos'][$j]['desc'];
				}
			}
		}
		else 
		{
			if (count($datos['tributos']) > 0)
			{
				$req['detalle']['tributos']['tributo']['id'] = $datos['tributos'][0]['id'];
				$req['detalle']['tributos']['tributo']['base_imp'] = Round($datos['tributos'][0]['base_imp'],2);
				$req['detalle']['tributos']['tributo']['importe'] = Round($datos['tributos'][0]['importe'],2);
				$req['detalle']['tributos']['tributo']['alicuota'] = $datos['tributos'][0]['alicuota'];
				$req['detalle']['tributos']['tributo']['desc'] = $datos['tributos'][0]['desc'];
			}
		}
	
		// Impuestos nacionales
		if (count($datos['impuestos']) > 1)
		{
			for ($j = 0; $j < count($datos['impuestos']); $j++)
			{
				if ($datos['impuestos'][$j]['importe'] != 0)
				{
					$req['detalle']['iva']['aliciva'][$j]['id'] = $datos['impuestos'][$j]['id'];
					$req['detalle']['iva']['aliciva'][$j]['base_imp'] = Round($datos['impuestos'][$j]['base_imp'],2);
					$req['detalle']['iva']['aliciva'][$j]['importe'] = Round($datos['impuestos'][$j]['importe'],2);
				}
			}
		}
		else
		{
			if ($datos['impuestos'][0]['importe'] != 0)
			{
				$req['detalle']['iva']['aliciva']['id'] = $datos['impuestos'][0]['id'];
				$req['detalle']['iva']['aliciva']['base_imp'] = Round($datos['impuestos'][0]['base_imp'],2);
				$req['detalle']['iva']['aliciva']['importe'] = Round($datos['impuestos'][0]['importe'],2);
			}
		}

		$base = "solicitaCAE-". Str::random(20);
		$nombreXml = $base.'.xml';
		$nombreXmlRespuesta = $base.'_RESP.xml';
		Storage::disk('public')->put("afip/ent/$nombreXml", $this->GenerarXML ($req, null));

		$ret = $this->ejecutaAfip($nombreXml);

		//Storage::disk('public')->delete("afip/ent/$nombreXml");
		
		$resp = new \SimpleXMLElement("storage/afip/ent/$nombreXmlRespuesta", null, true);

		$fechaVto = 0;
		if ($resp->cabecera->resultado == 'A')
		{
			$mensaje = $resp->mensaje;
			$tipo = $resp->cabecera->cbte_tipo;
			$resultado = $resp->cabecera->resultado;
			$pventa = $resp->cabecera->pto_venta;
			$numero = $resp->detalle->cbt_desde;
			
			$cae = ''; $fechaVto = '';
			if ($tipo == $tipotransaccion && $pventa == $puntoventa && $numero == $datos['numerocomprobante'])
			{
				$cae = $resp->detalle->cae;
				$fechaVto = $resp->detalle->cae_fch_vto;
			}
		}
		else // Verifica si el comprobante entro en AFIP
		{
			$ultimoNumero = Self::traeUltimoNumeroComprobante($nroinscripcion, $tipotransaccion, $puntoventa);

			if ($ultimoNumero + 1 == $datos['numerocomprobante'])
			{
				$resultado = Self::consultaCompEnviado($nroinscripcion, $tipotransaccion, $puntoventa,
														$datos['numerocomprobante']);

				if ($resultado != -1)
				{
					$cae = $resultado['cae'];
					$fechaVto = $resultado['fechavencimientocae'];
				}
			}
		}

		//Storage::disk('public')->delete("afip/ent/$nombreXmlRespuesta");

		if ($resultado == 'A')
			return ['cae' => $cae, 'fechavencimientocae' => $fechaVto];
		else	
			return 'Error';
	}

	public function consultaCompEnviado($nroinscripcion, $tipotransaccion, $puntoventa, $numero)
	{
		$req['solicitud']['servicio'] = 'wsfe_v1';
		$req['solicitud']['funcion'] = 'SolicitarUltimoCompEnviado';
		$req['datos']['cuit']   = (double) str_replace("-","",$nroinscripcion);
        $req['datos']['pventa'] = (int) $puntoventa;
        $req['datos']['tipo']   = (int) $tipotransaccion;
		$req['datos']['nro']   = (int) $numero;

		$base = "ultnrocomp-". Str::random(20);
		$nombreXml = $base.'.xml';
		$nombreXmlRespuesta = $base.'_RESP.xml';
		Storage::disk('public')->put("afip/ent/$nombreXml", $this->GenerarXML ($req, null));

		$ret = $this->ejecutaAfip($nombreXml);

		Storage::disk('public')->delete("afip/ent/$nombreXml");

		$resp = new \SimpleXMLElement("storage/afip/ent/$nombreXmlRespuesta", null, true);

		$mensaje = $resp->mensaje;
		$tipo = $resp->detalle->tipo;
		$pventa = $resp->detalle->pventa;
		$ultimoNumero = $resp->detalle->nro;
		$cae = $resp->detalle->cae;
		$fechavto = $resp->detalle->fevto;
		$resultado = $resp->detalle->resultado;

		Storage::disk('public')->delete("afip/ent/$nombreXmlRespuesta");

		if ($resultado == 'A')
			return ['cae' => $cae, 'fechavencimientocae' => $fechaVto];
		else
			return -1;
	}

	private function ejecutaAfip($nombrexml)
	{
		$path = Storage::path($nombrexml);

		$cmd = "cd storage/afip; ./afip.php ".str_replace(".xml", "", $nombrexml)." 1>&2 2>err";
		$process = new Process($cmd);
		$process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
		}
		echo $process->getOutput();
	}
	
	private function GenerarXML ($data, $xml) {
        if ($xml == null) {
            $xml = simplexml_load_string ("<?xml version='1.0' encoding='utf-8'?><req />");
        }

        foreach($data as $key => $value) {
            if (is_numeric($key)) {
                $key = "unknownNode_". (string) $key;
            }

            if (is_array($value)) {
                $node = $xml->addChild($key);

                $this->GenerarXML ($value, $node);

            } else {
                $value = htmlentities($value);
                $xml->addChild($key,$value);
            }
        }
		return $xml->asXML();
    }

	// Prepara tabla de tributos
	public function armaTributo($conceptosTotales, &$tributos, &$totalTributo)
	{
		$totalTributo = 0;
		foreach ($conceptosTotales as $concepto)
		{
			if ($concepto['concepto'] == 'Percepcion IVA')
			{
				$tributos[] = [
					'id' => 1,
					'base_imp' => $concepto['baseimponible'],
					'alicuota' => $concepto['tasa'],
					'desc' => $concepto['concepto'],
					'importe' => $concepto['importe']
				];
				$totalTributo += $concepto['importe'];
			}
			if (array_key_exists('jurisdiccion', $concepto))
			{
				$tributos[] = [
					'id' => 2,
					'base_imp' => $concepto['baseimponible'],
					'alicuota' => $concepto['tasa'],
					'desc' => $concepto['concepto'],
					'importe' => $concepto['importe']
				];
				$totalTributo += $concepto['importe'];
			}
		}
	}

	// Prepara tabla de impuestos
	public function armaImpuesto($conceptosTotales, &$impuestos)
	{
		foreach ($conceptosTotales as $concepto)
		{
			$pos = strpos($concepto['concepto'], 'Iva ');

			if ($pos >= 0 && $pos !== false)
			{
				$impuestos[] = [
					'id' => $concepto['codigo'],
					'base_imp' => $concepto['baseimponible'] ?? 0,
					'alicuota' => $concepto['tasa'],
					'desc' => $concepto['concepto'],
					'importe' => $concepto['importe']
				];
			}
		}
	}
}

