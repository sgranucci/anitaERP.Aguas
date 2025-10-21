<?php

namespace App\Repositories\Receptivo;

use App\Models\Receptivo\Ordenservicio;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Carbon\Carbon;
use Auth;
use DB;

class OrdenservicioRepository implements OrdenservicioRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Ordenservicio $ordenservicio)
    {
        $this->model = $ordenservicio;
    }

    public function create(array $data)
    {
        $ordenservicio = $this->model->create($data);

        return $ordenservicio;
    }

    public function update(array $data, $id)
    {
        $ordenservicio = $this->model->findOrFail($id)
            ->update($data);

		return $ordenservicio;
    }

    public function delete($id)
    {
    	$ordenservicio = $this->model->find($id);
		//
		// Elimina anita
		self::eliminarAnita($ordenservicio->codigo);

        $ordenservicio = $this->model->destroy($id);

		return $ordenservicio;
    }

    public function find($id)
    {
        $ordenservicio = $this->model->where('id', $id)->first();

        return $ordenservicio;
    }

    public function findOrFail($id)
    {
        if (null == $ordenservicio = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $ordenservicio;
    }

    public function consultaOrdenservicio($consulta)
    {
        $columns1 = ['voucher_guia.ordenservicio_id', 'guia.id', 'guia.codigo', 'guia.nombre'];
        $columns2 = ['caja_movimiento.ordenservicio_id'];
        $columnsOut = ['ordenservicio_id', 'guia_id', 'codigoguia', 'nombreguia'];

        $count = count($columns1);
        $voucher = DB::table('voucher_guia')->select('voucher_guia.ordenservicio_id as ordenservicio_id',
                                        'guia.id as guia_id',
                                        'guia.codigo as codigoguia',
                                        'guia.nombre as nombreguia')
                        ->join('guia', 'guia.id', 'voucher_guia.guia_id')
                        ->where('voucher_guia.deleted_at', null)
                        ->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                    ->from('rendicionreceptivo')
                                    ->where('rendicionreceptivo.deleted_at', null)
                                    ->whereColumn('voucher_guia.ordenservicio_id', 'rendicionreceptivo.ordenservicio_id');
                        })
                        ->where('voucher_guia.ordenservicio_id', '>', 0)
                        ->Where(function ($query) use ($count, $consulta, $columns1) {
                        			for ($i = 0; $i < $count; $i++)
                            			$query->orWhere($columns1[$i], "LIKE", '%'. $consulta . '%');
                        })
                        ->groupBy('voucher_guia.ordenservicio_id')
                        ->get();

        //$count2 = count($columns2);
		//$caja_movimiento = DB::table('caja_movimiento')->select('caja_movimiento.ordenservicio_id as ordenservicio_id',
        //                                        DB::raw('NULL AS guia_id'),
        //                                        DB::raw('NULL AS codigoguia'),
        //                                        DB::raw('NULL AS nombreguia'))
        //                ->where('caja_movimiento.ordenservicio_id', '>', 0)
		//				->whereNotExists(function ($query) {
		//					$query->select(DB::raw(1))
		//							->from('rendicionreceptivo')
		//							->where('deleted_at', null)
		//							->whereColumn('caja_movimiento.ordenservicio_id', 'rendicionreceptivo.ordenservicio_id');
		//				})
		//				->where('caja_movimiento.ordenservicio_id', '!=', null)
          //              ->orWhere(function ($query) use ($count2, $consulta, $columns2) {
            //            			for ($i = 0; $i < $count2; $i++)
              //              			$query->orWhere($columns2[$i], "LIKE", '%'. $consulta . '%');
                //        });
                        
        //$data = $voucher->union($caja_movimiento)->get();                        

        $data = $voucher;

        $output = [];
		$output['data'] = '';	
        $flSinDatos = true;
        $count = count($columns1);
		if (count($data) > 0)
		{
			foreach ($data as $row)
			{
                $flSinDatos = false;
                $output['data'] .= '<tr>';
                for ($i = 0; $i < $count; $i++)
                    $output['data'] .= '<td class="'.$columnsOut[$i].'">' . $row->{$columnsOut[$i]} . '</td>';	
                $output['data'] .= '<td><a class="btn btn-warning btn-sm eligeconsultaordenservicio">Elegir</a></td>';
                $output['data'] .= '</tr>';
			}
		}

        if ($flSinDatos)
		{
			$output['data'] .= '<tr>';
			$output['data'] .= '<td>Sin resultados</td>';
			$output['data'] .= '</tr>';
		}
		return(json_encode($output, JSON_UNESCAPED_UNICODE));
    }

    // lee una orden de servicio
    public function leeUnaOrdenservicio($ordenservicio_id)
    {
        $voucher = DB::table('voucher_guia')->select('voucher_guia.ordenservicio_id as ordenservicio_id',
                                        'guia.id as guia_id',
                                        'guia.codigo as codigoguia',
                                        'guia.nombre as nombreguia')
                        ->join('guia', 'guia.id', 'voucher_guia.guia_id')
                        ->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                    ->from('rendicionreceptivo')
                                    ->where('deleted_at', null)
                                    ->whereColumn('voucher_guia.ordenservicio_id', 'rendicionreceptivo.ordenservicio_id');
                        })
                        ->where('voucher_guia.ordenservicio_id', $ordenservicio_id)->get();

        return($voucher);
    }

    // Busca si la orden de servicio esta rendida

    public function leeOrdenservicioUsada($ordenservicio_id)
    {
        // lee una orden de servicio
        $ordenservicio = DB::table('rendicionreceptivo')
                                    ->select('id',
                                            'fecha', 
                                            'empresa_id')
                                    ->where('deleted_at', null)
                                    ->where('ordenservicio_id', $ordenservicio_id)
                                    ->get();

        return($ordenservicio);
    }
}
