<?php

namespace App\Repositories\Caja;

use App\Models\Caja\Voucher;
use App\Models\Caja\Voucher_Guia;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Auth;

class VoucherRepository implements VoucherRepositoryInterface
{
    protected $model, $model_voucher_guia;
    protected $tableAnita = 'reseserv';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = 'concc_concepto';

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Voucher $voucher,
                                Voucher_Guia $voucher_guia)

    {
        $this->model = $voucher;
        $this->model_voucher_guia = $voucher_guia;
    }

    public function all()
    {
        return $this->model->with('voucher_guias')->with('servicioterrestres')->with('proveedores')
                        ->with('formapagos')->with('monedas')->get();
    }

    public function leeVoucher($busqueda, $flPaginando = null)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $vouchers = $this->model->select('voucher.id as id',
                                        'voucher.numero as numerovoucher',
                                        'talonariovoucher.id as idtalonario',
                                        'talonariovoucher.nombre as nombretalonario',
                                        'voucher.fecha as fecha',
                                        'voucher.nombrepasajero as nombrepasajero',
                                        'voucher.reserva_id as numeroreserva',
                                        'voucher.pax as pax',
                                        'voucher.paxfree as paxfree',
                                        'voucher.incluido as incluido',
                                        'voucher.opcional as opcional',
                                        'proveedor.nombre as nombreproveedor',
                                        'servicioterrestre.nombre as nombreservicio',
                                        'formapago.nombre as nombreformapago',
                                        'voucher.montovoucher as montovoucher')
                                ->join('talonariovoucher', 'talonariovoucher.id', '=', 'voucher.talonariovoucher_id')
                                ->join('proveedor', 'proveedor.id', '=', 'voucher.proveedor_id')
                                ->join('servicioterrestre', 'servicioterrestre.id', '=', 'voucher.servicioterrestre_id')
                                ->join('formapago', 'formapago.id', '=', 'voucher.formapago_id')
                                ->with('voucher_guias')
                                ->where('voucher.numero', $busqueda)
                                ->orWhere('talonariovoucher.id', $busqueda)
                                ->orWhere('talonariovoucher.nombre', 'like', '%'.$busqueda.'%')  
                                ->orWhere('voucher.fecha', $busqueda)  
                                ->orWhere('voucher.nombrepasajero', 'like', '%'.$busqueda.'%')  
                                ->orWhere('voucher.montovoucher', $busqueda)
                                ->orWhere('voucher.reserva_id', $busqueda)
                                ->orWhere('proveedor.nombre', 'like', '%'.$busqueda.'%')
                                ->orWhere('servicioterrestre.nombre', 'like', '%'.$busqueda.'%')
                                ->orWhere('formapago.nombre', 'like', '%'.$busqueda.'%')
                                ->orWhere('voucher.montovoucher', $busqueda)
                                ->orderby('id', 'DESC');
                                
        if (isset($flPaginando))
        {
            if ($flPaginando)
                $vouchers = $vouchers->paginate(10);
            else
                $vouchers = $vouchers->get();
        }
        else
            $vouchers = $vouchers->get();

        return $vouchers;
    }

    public function create(array $data)
    {
        $ultimoNumero = Self::leeUltimoNumero($data['talonariovoucher_id']);
        $data['numero'] = $ultimoNumero + 1;

        $voucher = $this->model->create($data);
		//
		// Graba anita
		self::guardarAnita($data);

        return $voucher;
    }

    public function update(array $data, $id)
    {
        $voucher = $this->model->findOrFail($id)
            ->update($data);
		//
		// Actualiza anita
		self::actualizarAnita($data, $id);

		return $voucher;
    }

    public function delete($id)
    {
    	$voucher = Voucher::find($id);
		//
		// Elimina anita
		self::eliminarAnita($voucher->codigo);

        $voucher = $this->model->destroy($id);

		return $voucher;
    }

    public function find($id)
    {
        if (null == $voucher = $this->model->with('voucher_guias')->with('servicioterrestres')->with('proveedores')
                                            ->with('formapagos')->with('monedas')
                                            ->with('talonariovouchers')->find($id)) 
        {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $voucher;
    }

    public function findOrFail($id)
    {
        if (null == $voucher = $this->model->with('voucher_guias')->with('servicioterrestres')->with('proveedores')
                                            ->with('formapagos')->with('monedas')
                                            ->with('talonariovouchers')->findOrFail($id)) 
        {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $voucher;
    }

    public function leeUltimoNumero($talonariovoucher_id)
    {
        $voucher = $this->model->select('numero')->where('talonariovoucher_id', $talonariovoucher_id)
                                ->orderBy('numero', 'desc')->first();

        if ($voucher)
            return $voucher->numero;

        return(0);
    }

	public function guardarAnita($request) {
    
	}

	public function actualizarAnita($request, $id) {
	}

    private function armaVariablesParaGrabar()
    {
    }

	public function eliminarAnita($id) {
	}
	
}
