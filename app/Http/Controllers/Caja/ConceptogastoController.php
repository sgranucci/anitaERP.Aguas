<?php

namespace App\Http\Controllers\Caja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Caja\Conceptogasto;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionConceptogasto;
use App\Repositories\Caja\ConceptogastoRepositoryInterface;

class ConceptogastoController extends Controller
{
	private $repository;

    public function __construct(ConceptogastoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-conceptos-de-gastos');
		$datas = $this->repository->all();

        return view('caja.conceptogasto.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-conceptos-de-gastos');

        return view('caja.conceptogasto.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionConceptogasto $request)
    {
		$this->repository->create($request->all());

        return redirect('caja/conceptogasto')->with('mensaje', 'Concepto de gasto creado con éxito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-conceptos-de-gastos');
        $data = $this->repository->findOrFail($id);

        return view('caja.conceptogasto.editar', compact('data'));
    }

    /**
     * Updote the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionConceptogasto $request, $id)
    {
        can('actualizar-conceptos-de-gastos');

        $this->repository->update($request->all(), $id);

        return redirect('caja/conceptogasto')->with('mensaje', 'Concepto de gasto actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-conceptos-de-gastos');

        if ($request->ajax()) {
        	if ($this->repository->delete($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}
