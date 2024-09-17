<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Configuracion\Impuesto;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionImpuesto;
use App\Repositories\Configuracion\ImpuestoRepositoryInterface;
use Carbon\Carbon;

class ImpuestoController extends Controller
{
    private $impuestoRepository;

    public function __construct(ImpuestoRepositoryInterface $impuestorepository)
    {
        $this->impuestoRepository = $impuestorepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-impuestos');

        $datas = $this->impuestoRepository->all();

        return view('configuracion.impuesto.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-impuestos');
        return view('configuracion.impuesto.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionImpuesto $request)
    {
        $this->impuestoRepository->create($request->all());

        return redirect('configuracion/impuesto')->with('mensaje', 'Impuesto creado con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-impuestos');

        $data = $this->impuestoRepository->findOrFail($id);

        return view('configuracion.impuesto.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionImpuesto $request, $id)
    {
        can('actualizar-impuestos');

        $this->impuestoRepository->update($request->all(), $id);

        return redirect('configuracion/impuesto')->with('mensaje', 'Impuesto actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-impuestos');

        if ($request->ajax()) {
            if ($this->impuestoRepository->delete($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        return redirect('configuracion/impuesto')->with('mensaje', 'Impuesto eliminado con exito');
    }
}
