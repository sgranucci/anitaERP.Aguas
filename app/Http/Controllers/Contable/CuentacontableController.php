<?php

namespace App\Http\Controllers\Contable;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contable\Cuentacontable;
use App\Models\Contable\Rubrocontable;
use App\Http\Requests\ValidacionCuentacontable;
use App\Repositories\Caja\ConceptogastoRepositoryInterface;
use App\Repositories\Contable\CuentacontableRepositoryInterface;
use App\Repositories\Contable\Cuentacontable_CentrocostoRepositoryInterface;
use App\Repositories\Contable\CentrocostoRepositoryInterface;
use App\Repositories\Configuracion\EmpresaRepositoryInterface;
use DB;

class CuentacontableController extends Controller
{
    private $conceptogastoRepository;
    private $cuentacontableRepository;
    private $empresaRepository;
    private $cuentacontable_centrocostoRepository;
    private $centrocostoRepository;
 
    public function __construct(ConceptogastoRepositoryInterface $conceptogastorepository,
                                CuentacontableRepositoryInterface $cuentacontablerepository,
                                EmpresaRepositoryInterface $empresarepository,
                                CentrocostoRepositoryInterface $centrocostorepository,
                                Cuentacontable_CentrocostoRepositoryInterface $cuentacontable_centrocostorepository)
    {
        $this->conceptogastoRepository = $conceptogastorepository;
        $this->cuentacontableRepository = $cuentacontablerepository;
        $this->empresaRepository = $empresarepository;
        $this->centrocostoRepository = $centrocostorepository;
        $this->cuentacontable_centrocostoRepository = $cuentacontable_centrocostorepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cuentas-contables');

        $cuentacontables = $this->cuentacontableRepository->all();

        return view('contable.cuentacontable.index', compact('cuentacontables'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-cuentas-contables');
		$rubrocontable_query = Rubrocontable::all();
        $empresa_query = $this->empresaRepository->all();
        $cuentacontable_query = $this->cuentacontableRepository->all();
        $conceptogasto_query = $this->conceptogastoRepository->all();
        $centrocosto_query = $this->centrocostoRepository->all();
        $ajustamonedaextranjera_enum = CuentaContable::$enumAjustaMonedaExtranjera;

        return view('contable.cuentacontable.crear', compact('rubrocontable_query', 'empresa_query',
                                                        'conceptogasto_query', 'cuentacontable_query',
                                                        'centrocosto_query', 'ajustamonedaextranjera_enum'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionCuentacontable $request)
    {
        DB::beginTransaction();
        try
        {
            $cuentacontable = $this->cuentacontableRepository->all($request->all());

            // Guarda tablas asociadas
            if ($cuentacontable)
                $cuentacontable_centrocosto = $this->cuentacontable_centrocostoRepository->create($request->all(), $cuentacontable->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return ['errores' => $e->getMessage()];
        }

        return redirect('contable/cuentacontable/crear')->with('mensaje', 'Cuenta contable creada con exito');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-cuentas-contables');

        $data = $this->cuentacontableRepository->findOrFail($id);
		$rubrocontable_query = Rubrocontable::all();
        $empresa_query = $this->empresaRepository->all();
        $cuentacontable_query = $this->cuentacontableRepository->all();
        $conceptogasto_query = $this->conceptogastoRepository->all();
        $centrocosto_query = $this->centrocostoRepository->all();
        $ajustamonedaextranjera_enum = CuentaContable::$enumAjustaMonedaExtranjera;

        return view('contable.cuentacontable.editar', compact('data', 'rubrocontable_query', 
                                                'empresa_query', 'conceptogasto_query', 'cuentacontable_query',
                                                'centrocosto_query', 'ajustamonedaextranjera_enum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionCuentacontable $request, $id)
    {
        can('actualizar-cuentas-contables');

        DB::beginTransaction();
        try
        {
            // Graba cuenta contable
            $this->cuentacontableRepository->update($request->all(), $id);

            // Graba centros de costos
            $this->cuentacontable_centrocostoRepository->update($request->all(), $id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            dd($e->getMessage());
            return ['errores' => $e->getMessage()];
        }

        return redirect('contable/cuentacontable')->with('mensaje', 'Cuenta actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar($id)
    {
        can('borrar-cuentas-contables');

        if ($request->ajax()) {
        	if ($this->cuentacontableRepository->delete($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        return redirect('contable/cuentacontable')->with('mensaje', 'Cuenta eliminada con exito');
    }

    public function consultaCuentaContable(Request $request)
    {
		$columns = ['cuentacontable.id', 'cuentacontable.codigo', 'cuentacontable.nombre', 'empresa.nombre'];
		$columnsOut = ['cuentacontable_id', 'codigo', 'nombre', 'nombreempresa'];

		$query = CuentaContable::select('cuentacontable.id as cuentacontable_id', 'cuentacontable.codigo', 
                'cuentacontable.nombre', 'empresa.nombre as nombreempresa')
				->leftJoin('empresa','cuentacontable.empresa_id','=','empresa.id')
                ->where('tipocuenta', '1');

		$consulta = $request->consulta;

		/* Filtrado */
		$cont = count($columns);
		if ($consulta != null) 
		{
			$query = $query->where($columns[0], "LIKE", '%'. $consulta . '%');

			for ($i = 1; $i < $cont; $i++) {
				$query = $query->orWhere($columns[$i], "LIKE", '%'. $consulta . '%');
			}
		}
		$query = $query->get();

		$output = [];
		$output['data'] = '';	
		if (count($query) > 0)
		{
			foreach ($query as $row)
			{
				$output['data'] .= '<tr>';
				for ($i = 0; $i < $cont; $i++)
				{
					$output['data'] .= '<td class="'.$columnsOut[$i].'">' . $row[$columnsOut[$i]] . '</td>';	
				}
				$output['data'] .= '<td><a class="btn btn-warning btn-sm eligeconsulta">Elegir</a></td>';
				$output['data'] .= '</tr>';
			}
		}
		else
		{
			$output['data'] .= '<tr>';
			$output['data'] .= '<td>Sin resultados</td>';
			$output['data'] .= '</tr>';
		}
		return(json_encode($output, JSON_UNESCAPED_UNICODE));
	}

    public function leerCuentaContablePorCodigo($empresa_id, $codigo)
    {
        return $this->cuentacontableRepository->findPorCodigo($empresa_id, $codigo);
    }

    public function leerCuentaContableCentroCosto($cuentacontable_id)
    {
        $cuentacontable = $this->cuentacontableRepository->find($cuentacontable_id);

        // Busca los centros de costo asociados
        if ($cuentacontable)
        {
            if ($cuentacontable->manejaccosto === '1' || $cuentacontable->manejaccosto === 'S')
            {
                $centrocosto = $this->cuentacontable_centrocostoRepository->leeCuentacontable_Centrocosto($cuentacontable->id);

                if (count($centrocosto) > 0)
                    return($centrocosto);
                else
                    return ($this->centrocostoRepository->all());
            }
            else
            {
                return 'No maneja centro de costo';
            }
        }
        return 'Cuenta inexistente';
    }

}
