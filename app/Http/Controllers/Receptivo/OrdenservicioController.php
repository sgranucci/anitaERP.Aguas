<?php

namespace App\Http\Controllers\Receptivo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionOrdenservicio;
use App\Repositories\Receptivo\OrdenservicioRepositoryInterface;

class OrdenservicioController extends Controller
{
    private $prdenservicioRepository;

	public function __construct(OrdenservicioRepositoryInterface $ordenserviciorepository)
    {
        $this->ordenservicioRepository = $ordenserviciorepository;
    }

    public function consultaOrdenservicio(Request $request)
    {
        return ($this->ordenservicioRepository->consultaOrdenservicio($request->consulta));
	}

    public function leeUnaOrdenservicio($ordenservicio_id)
    {
        return ($this->ordenservicioRepository->leeUnaOrdenservicio($ordenservicio_id));
	}

    public function leeOrdenservicioUsada($ordenservicio_id)
    {
        return $this->ordenservicioRepository->leeOrdenservicioUsada($ordenservicio_id);
    }
}
