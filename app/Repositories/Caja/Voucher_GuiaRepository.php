<?php

namespace App\Repositories\Caja;

use App\Models\Caja\Voucher_Guia;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Auth;

class Voucher_GuiaRepository implements Voucher_GuiaRepositoryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Voucher_Guia $voucher_guia)
    {
        $this->model = $voucher_guia;
    }

    public function create(array $data, $id)
    {
		return self::guardarVoucher_Guia($data, 'create', $id);
    }

    public function update(array $data, $id)
    {
		return self::guardarVoucher_Guia($data, 'update', $id);
    }

    public function delete($voucher_id, $codigo)
    {
        $voucher_guia = $this->model->where('voucher_id', $voucher_id)->delete();

		return $proveedor;
    }

    public function find($id)
    {
        if (null == $voucher_guia = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $proveedor;
    }

	public function leeVoucherGuia($voucher_id)
	{
		$voucher_guia = $this->model->where('voucher_id', $voucher_id)->get();

		return $voucher_guia;
	}
	
    public function findOrFail($id)
    {
        if (null == $voucher_guia = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $proveedor;
    }

	private function guardarVoucher_Guia($data, $funcion, $id = null)
	{
		if ($funcion == 'update')
		{
			// Trae todos los id
        	$voucher_guia = $this->model->where('voucher_id', $id)->get()->pluck('id')->toArray();
			$q_voucher_guia = count($voucher_guia);
		}

		// Graba exclusiones
		if (isset($data['guia_ids']))
		{
			$guia_ids = $data['guia_ids'];
			$tipocomisiones = $data['tipocomisiones'];
			$porcentajecomisiones = $data['porcentajecomisiones'];
			$montocomisiones = $data['montocomisiones'];

			if ($funcion == 'update')
			{
				$_id = $voucher_guia;

				// Borra los que sobran
				if ($q_voucher_guia > count($guia_ids))
				{
					for ($d = count($guia_ids); $d < $q_voucher_guia; $d++)
						$this->model->find($_id[$d])->delete();
				}

				// Actualiza los que ya existian
				for ($i = 0; $i < $q_voucher_guia && $i < count($guia_ids); $i++)
				{
					if ($i < count($guia_ids))
					{
						$voucher_guia = $this->model->findOrFail($_id[$i])->update([
									"voucher_id" => $id,
									"guia_id" => $guia_ids[$i],
									"tipocomision" => $tipocomisiones[$i],
									"porcentajecomision" => $porcentajecomisiones[$i],
									"montocomision" => $montocomisiones[$i],
									]);
					}
				}
				if ($q_voucher_guia > count($guia_ids))
					$i = $d; 
			}
			else
				$i = 0;

			for ($i_guia = $i; $i_guia < count($guia_ids); $i_guia++)
			{
				//* Valida si se cargo una exclusion
				if ($guia_ids[$i_guia] != '') 
				{
					$voucher_guia = $this->model->create([
									"voucher_id" => $id,
									"guia_id" => $guia_ids[$i_guia],
									"tipocomision" => $tipocomisiones[$i_guia],
									"porcentajecomision" => $porcentajecomisiones[$i_guia],
									"montocomision" => $montocomisiones[$i_guia],
									]);
				}
			}
		}
		else
		{
			$voucher_guia = $this->model->where('voucher_id', $id)->delete();
		}
	}
}
