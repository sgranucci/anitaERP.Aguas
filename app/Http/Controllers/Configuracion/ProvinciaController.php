<?php

namespace App\Http\Controllers\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ValidacionProvincia;
use App\Repositories\Configuracion\ProvinciaRepositoryInterface;
use App\Models\Configuracion\Pais;

class ProvinciaController extends Controller
{
    private $provinciaRepository;

    public function __construct(ProvinciaRepositoryInterface $provinciarepository)
    {
        $this->provinciaRepository = $provinciarepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-provincias');

        $datas = $this->provinciaRepository->all();

        return view('configuracion.provincia.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-provincias');

		$pais_query = Pais::all();

        return view('configuracion.provincia.crear', compact('pais_query'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidacionProvincia $request)
    {
        $this->provinciaRepository->create($request->all());

        return redirect('configuracion/provincia')->with('mensaje', 'Provincia creada con exito');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-provincias');
		$pais_query = Pais::all();
        $data = $this->provinciaRepository->findOrFail($id);
		
        return view('configuracion.provincia.editar', compact('data', 'pais_query'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionProvincia $request, $id)
    {
        can('actualizar-provincias');

        $this->provinciaRepository->update($request->all(), $id);

        return redirect('configuracion/provincia')->with('mensaje', 'Provincia actualizada con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        can('borrar-provincias');

        if ($request->ajax()) {
            if ($this->provinciaRepository->delete($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
        return redirect('configuracion/provincia')->with('mensaje', 'Provincia eliminada con exito');
    }
}
