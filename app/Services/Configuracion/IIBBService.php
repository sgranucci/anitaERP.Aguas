<?php
namespace App\Services\Configuracion;

use App\Models\Configuracion\Padronarba;
use App\Models\Configuracion\Padroncaba;
use App\Models\Configuracion\Provincia;
use App\Repositories\Configuracion\PadronarbaRepositoryInterface;
use App\Repositories\Configuracion\PadroncabaRepositoryInterface;

class IIBBService 
{
	protected $padronarbaRepository;
	protected $padroncabaRepository;

	private $tasapercepcion;
	private $flLeyoPadron;

	public function __construct(PadronarbaRepositoryInterface $padronarbaRepository, 
								PadroncabaRepositoryInterface $padroncabaRepository)
	{
		$this->padronarbaRepository = $padronarbaRepository;
		$this->padroncabaRepository = $padroncabaRepository;
	}

	public function leeTasaPercepcion($nroinscripcion, $jurisdiccion)
	{
		$tasapercepcion = 0;
		$this->flLeyoPadron = false;

		switch($jurisdiccion)
		{
		case '901':
			$tasapercepcion = $this->padroncabaRepository->leePadronCaba($nroinscripcion, 'percepcion');
			if ($tasapercepcion)
				$this->flLeyoPadron = true;
			break;
		case '902':
			$tasapercepcion = $this->padronarbaRepository->leePadronArba($nroinscripcion, 'percepcion');
			if ($tasapercepcion)
				$this->flLeyoPadron = true;
			break;
		}

		return $tasapercepcion;
	}

	// Calcula percepciones de ingresos brutos para ventas

	public function calculaPercepcionIIBB($totalNeto, $nroinscripcion, $condicionIIBB, $provinciaInscripcion)
	{
		$percepcionesIIBB = [];

		if ($condicionIIBB != 'N')
		{
			$provinciasPercepcion = explode(",", env("ANITA_AGENTE_PERCEPCION_IIBB"));
			$tasasDescarte = explode(",", env("ANITA_TASAS_DESCARTE_IIBB"));
			$minimoNeto = explode(",", env("ANITA_MINIMO_NETO_IIBB"));
			$minimaPercepcion = explode(",", env("ANITA_MINIMA_PERCEPCION_IIBB"));
			$percepcionesIIBB = [];
			for ($i = 0; $i < count($provinciasPercepcion); $i++)
			{
				if ($totalNeto >= $minimoNeto[$i])
				{
					$tasa = self::leeTasaPercepcion($nroinscripcion, $provinciasPercepcion[$i]);

					if (!$this->flLeyoPadron)
						$tasa = $tasasDescarte[$i];

					$importePercepcion = $totalNeto * $tasa / 100.;
					
					//if ($i == 1)
					//	dd($totalNeto.' '.$minimoNeto[$i].' '.$importePercepcion.' '.$minimaPercepcion[$i].' '.$i.' '.$tasa);

					if ($importePercepcion >= $minimaPercepcion[$i] && $importePercepcion != 0)
					{
						$provincia = Provincia::where("jurisdiccion",$provinciasPercepcion[$i])->first();

						$concepto = "Perc. ".$provincia->nombre." ".($tasa < 0.00001 ? " " : $tasa."%");
						if ($provincia && $importePercepcion != 0)
						{
							$percepcionesIIBB[] = ["concepto"=>$concepto,
												"tasa"=>($tasa < 0.0001 ? 0 : $tasa),
												"baseimponible"=>$totalNeto,
												"jurisdiccion"=>$provinciasPercepcion[$i],
												"provincia_id"=>$provincia->id,
												"importe"=>$importePercepcion,
											];
						}
					}
				}
			}
		}
		return $percepcionesIIBB;
	}
}

