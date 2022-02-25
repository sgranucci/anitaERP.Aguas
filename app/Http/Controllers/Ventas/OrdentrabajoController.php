<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Queries\Ventas\OrdentrabajoQueryInterface;
use App\Services\Ventas\OrdentrabajoService;

class OrdentrabajoController extends Controller
{
	private $ordentrabajoQuery;
	private $ordentrabajoService;

    public function __construct(
    	OrdentrabajoService $ordentrabajoservice,
		OrdentrabajoQueryInterface $ordentrabajoquery)
    {
        $this->middleware('auth');

        $this->ordentrabajoService = $ordentrabajoservice;
        $this->ordentrabajoQuery = $ordentrabajoquery;
    }

    public function index()
    {
		$ordentrabajo_query = $this->ordentrabajoService->leeOrdenestrabajoPendientes();

        return view('ventas.repetiquetaot.create', compact('ordentrabajo_query'));
    }

    public function crearEtiquetaOt(Request $request)
    {
		return $this->ordentrabajoService->listaEtiquetaOT($request->all());
    }
}
