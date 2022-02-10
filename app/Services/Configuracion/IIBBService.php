<?php
namespace App\Services\Configuracion;

use App\Models\Configuracion\Padronarba;
use App\Models\Configuracion\Padroncaba;
use App\Repositories\Configuracion\PadronarbaRepositoryInterface;
use App\Repositories\Configuracion\PadroncabaRepositoryInterface;

class IIBBService 
{
	protected $padronarbaRepository;
	protected $padroncabaRepository;

	private $tasapercepcion;

	public function __construct(PadronarbaRepositoryInterface $padronarbaRepository, 
								PadroncabaRepositoryInterface $padroncabaRepository)
	{
		$this->padronarbaRepository = $padronarbaRepository;
		$this->padroncabaRepository = $padroncabaRepository;
	}

	public function leeTasaPercepcion($cuit, $jurisdiccion)
	{
		$tasapercepcion = 0;

		switch($jurisdiccion)
		{
		case '901':
			$tasapercepcion = $this->padroncabaRepository->leePadronCaba($cuit, 'percepcion');
			break;
		case '902':
			$tasapercepcion = $this->padronarbaRepository->leePadronArba($cuit, 'percepcion');
			break;
		}

		return $tasapercepcion;
	}
}

