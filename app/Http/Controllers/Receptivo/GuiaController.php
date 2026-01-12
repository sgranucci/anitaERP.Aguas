<?php

namespace App\Http\Controllers\Receptivo;

use App\Http\Controllers\Controller;
use App\Http\Requests\EliminarMasivoGuiaRequest;
use App\Http\Requests\GuardarGuiaRequest;
use App\Http\Requests\ActualizarGuiaRequest;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Localidad;
use App\Models\Configuracion\Provincia;
use App\Models\Receptivo\Guia;
use App\Repositories\Receptivo\GuiaRepositoryInterface;
use App\Repositories\Receptivo\Guia_CuentacorrienteRepositoryInterface;
use App\Repositories\Receptivo\Guia_IdiomaRepositoryInterface;
use App\Repositories\Receptivo\IdiomaRepositoryInterface;
use App\Exports\Receptivo\Guia_CuentacorrienteExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

class GuiaController extends Controller
{
	private $guiaRepository, $guia_idiomaRepository;
    private $guia_cuentacorrienteRepository;
    private $idiomaRepository;

	public function __construct(GuiaRepositoryInterface $guiarepository,
								Guia_IdiomaRepositoryInterface $guia_idiomarepository,
                                Guia_CuentacorrienteRepositoryInterface $guia_cuentacorrienteRepository,
                                IdiomaRepositoryInterface $idiomarepository)
    {
        $this->guiaRepository = $guiarepository;
		$this->guia_idiomaRepository = $guia_idiomarepository;
        $this->guia_cuentacorrienteRepository = $guia_cuentacorrienteRepository;
        $this->idiomaRepository = $idiomarepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-guia');
		
		$guias = $this->guiaRepository->all();

		$maneja_enum = Guia::$enumManeja;
        $tipoguia_enum = Guia::$enumTipoGuia;
        $tipodocumento_enum = config('enums.tipodocumento');
        $idioma_query = $this->idiomaRepository->all();

        return view('receptivo.guia.index', compact('guias', 'maneja_enum', 'tipoguia_enum', 'tipodocumento_enum', 'idioma_query'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-guia');

		$maneja_enum = Guia::$enumManeja;
        $tipoguia_enum = Guia::$enumTipoGuia;
        $tipodocumento_enum = config('enums.tipodocumento');
        $idioma_query = $this->idiomaRepository->all();
        $pais_query = Pais::orderBy('nombre')->get();
        $provincia_query = Provincia::orderBy('nombre')->get();

        return view('receptivo.guia.crear', compact('maneja_enum', 'tipoguia_enum', 'tipodocumento_enum','idioma_query', 'pais_query',
                                    'provincia_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(GuardarGuiaRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $guia = $this->guiaRepository->create($request->all());

            if ($guia)
            {
                $idiomas = $request->input('idioma_ids', []);
                for ($i_idioma=0; $i_idioma < count($idiomas); $i_idioma++) {
                    if ($idiomas[$i_idioma] > 0)
                    {
                        $guia_idioma = $this->guia_idiomaRepository->create([
                                                            'guia_id' => $guia->id,
                                                            'idioma_id' => $idiomas[$i_idioma], 
                                                            ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['errores' => $e->getMessage()];
        }

    	return redirect('receptivo/guia')->with('mensaje', 'Guia creado con exito');
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-guia');

		$guia = $this->guiaRepository->find($id);

		$maneja_enum = Guia::$enumManeja;
        $tipoguia_enum = Guia::$enumTipoGuia;
        $tipodocumento_enum = config('enums.tipodocumento');
        $idioma_query = $this->idiomaRepository->all();
        $pais_query = Pais::orderBy('nombre')->get();
        $provincia_query = Provincia::orderBy('nombre')->get();

        return view('receptivo.guia.editar', compact('guia', 'maneja_enum', 'tipoguia_enum', 'tipodocumento_enum', 'idioma_query',
                                                'pais_query', 'provincia_query'));
    }

    /**
     * Updote the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ActualizarGuiaRequest $request, $id)
    {
        can('actualizar-guia');

        DB::beginTransaction();
        try
        {
            $this->guiaRepository->update($request->all(), $id);

            $guia_idioma = $this->guia_idiomaRepository->deletePorGuia($id);

            $idiomas = $request->input('idioma_ids', []);
            for ($i_idioma=0; $i_idioma < count($idiomas); $i_idioma++) {
                if ($idiomas[$i_idioma] > 0)
                {
                    $guia_idioma = $this->guia_idiomaRepository->create([
                                                        'guia_id' => $id,
                                                        'idioma_id' => $idiomas[$i_idioma], 
                                                        ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['errores' => $e->getMessage()];
        }

		return redirect('receptivo/guia')->with('mensaje', 'Guia actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-guia');

        if ($request->ajax()) 
		{
			$fl_borro = false;
			if ($this->guiaRepository->delete($id))
				$fl_borro = true;

            if ($fl_borro) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function consultaGuia(Request $request)
    {
        return ($this->guiaRepository->leeGuia($request->consulta));
	}

    public function leeGuia($codigoguia)
    {
        return ($this->guiaRepository->findPorCodigo($codigoguia));
	}

    public function listarCuentaCorriente(Request $request, $guia_id)
    {
        can('listar-cuentacorriente-guia');

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $formato = $request->formato;
        $busqueda = $request->busqueda;
        $guia = $this->guiaRepository->find($guia_id);

        $nombreguia = '';
        if ($guia)
            $nombreguia= $guia->nombre;

        switch($formato)
        {
        case 'PDF':
            $cuentacorriente = $this->guia_cuentacorrienteRepository->listarCuentaCorriente($busqueda, $guia_id, false);

            $view =  \View::make('receptivo.cuentacorrienteguia.listado', compact('cuentacorriente', 'nombreguia'))
                        ->render();
            $path = storage_path('pdf/listados');
            $nombre_pdf = 'listado_cuentacorriente_guia';

            $pdf = \App::make('dompdf.wrapper');
            $pdf->setPaper('legal','landscape');
            $pdf->loadHTML($view)->save($path.'/'.$nombre_pdf.'.pdf');

            return response()->download($path.'/'.$nombre_pdf.'.pdf');
            break;

        case 'EXCEL':
            return (new Guia_CuentacorrienteExport($this->guia_cuentacorrienteRepository, $guia_id, $nombreguia))
                        ->parametros($busqueda)
                        ->download('cuentacorrienteguia.xlsx');
            break;

        case 'CSV':
            return (new Guia_CuentacorrienteExport($this->guia_cuentacorrienteRepository, $guia_id, $nombreguia))
                        ->parametros($busqueda)
                        ->download('cuentacorrienteguia.csv', \Maatwebsite\Excel\Excel::CSV);
            break;

        default:
            $cuentacorriente = $this->guia_cuentacorrienteRepository->listarCuentaCorriente($busqueda, $guia_id, true);

            $datas = ['cuentacorriente' => $cuentacorriente, 'busqueda' => $busqueda, 
                        'id' => $guia_id, 'nombreguia' => $nombreguia];

            return view('receptivo.cuentacorrienteguia.index', $datas);
        }
    }

    // Editar cuenta corriente
    public function EditarCuentaCorriente($cuentacorriente_id)
    {
        $cuentacorriente = $this->guia_cuentacorrienteRepository->find($cuentacorriente_id);

        // Verifica si es movimiento de caja o rendicion
        if ($cuentacorriente->rendicionreceptivo_id != null)
            return $this->rendicion->editaUnaFactura($cuentacorriente->venta_id);

        return false;
    }     
}
