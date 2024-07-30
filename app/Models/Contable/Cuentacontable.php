<?php

namespace App\Models\Contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;
use Auth;

class Cuentacontable extends Model
{
    protected $fillable = ['empresa_id', 'rubrocontable_id', 'cuentacontable_id', 'orden', 'nivel', 'nombre', 'codigo', 'tipocuenta', 'monetaria', 'manejaccosto', 'usuarioultcambio_id'];
    protected $table = 'cuentacontable';
    protected $tableAnita = 'ctamae';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = 'ctam_cuenta';

    public function empresas()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }

    public function rubroscontables()
    {
        return $this->belongsTo(Rubrocontable::class, 'rubrocontable_id');
    }

    public function getHijos($padres, $line)
    {
        $children = [];
        foreach ($padres as $line1) {
            if ($line['id'] == $line1['cuentacontable_id']) {
                $children = array_merge($children, [array_merge($line1, ['subcuentacontable' => $this->getHijos($padres, $line1)])]);
            }
        }
        return $children;
    }

    public function getPadres($front)
    {
       return $this->orderby('cuentacontable_id')
                ->orderby('orden')
                ->get()
                ->toArray();
    }

    public static function getCuentacontable($front = false)
    {
        $cuentas = new Cuentacontable();
        $padres = $cuentas->getPadres($front);
        $cuentaAll = [];
        foreach ($padres as $line) {
            if ($line['cuentacontable_id'] != 0)
                break;
            $item = [array_merge($line, ['subcuentacontable' => $cuentas->getHijos($padres, $line)])];
            $cuentaAll = array_merge($cuentaAll, $item);
        }
        return $cuentaAll;
    }

    public function guardarOrden($cuenta)
    {
        $cuentas = json_decode($cuenta);
        foreach ($cuentas as $var => $value) {
            $this->where('id', $value->id)->update(['cuentacontable_id' => 0, 'orden' => $var + 1]);
            if (!empty($value->children)) {
                foreach ($value->children as $key => $vchild) {
                    $update_id = $vchild->id;
                    $parent_id = $value->id;
                    $this->where('id', $update_id)->update(['cuentacontable_id' => $parent_id, 'orden' => $key + 1]);

                    if (!empty($vchild->children)) {
                        foreach ($vchild->children as $key => $vchild1) {
                            $update_id = $vchild1->id;
                            $parent_id = $vchild->id;
                            $this->where('id', $update_id)->update(['cuentacontable_id' => $parent_id, 'orden' => $key + 1]);

                            if (!empty($vchild1->children)) {
                                foreach ($vchild1->children as $key => $vchild2) {
                                    $update_id = $vchild2->id;
                                    $parent_id = $vchild1->id;
                                    $this->where('id', $update_id)->update(['cuentacontable_id' => $parent_id, 'orden' => $key + 1]);

                                    if (!empty($vchild2->children)) {
                                        foreach ($vchild2->children as $key => $vchild3) {
                                            $update_id = $vchild3->id;
                                            $parent_id = $vchild2->id;
                                            $this->where('id', $update_id)->update(['cuentacontable_id' => $parent_id, 'orden' => $key + 1]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'campos' => 'ctam_empresa, '.$this->keyFieldAnita, 
						'tabla' => $this->tableAnita, 
						'orderBy' => 'ctam_empresa, '.$this->keyFieldAnita  );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        $datosLocal = Cuentacontable::all();
        $datosLocalArray = [];
        foreach ($datosLocal as $value) {
            $datosLocalArray[] = $value->{$this->keyField};
        }
        
		if ($dataAnita)
		{
        	foreach ($dataAnita as $value) {
            	if (!in_array($value->{$this->keyFieldAnita}, $datosLocalArray)) {
                	$this->traerRegistroDeAnita($value->{$this->keyFieldAnita});
            	}
        	}
		}
    }

    public function traerRegistroDeAnita($key){
		static $nivel = 0, $id = 0, $titulo_id = 0;

        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'campos' => '
                ctam_empresa,
				ctam_cuenta,
				ctam_tipo,
				ctam_desc,
				ctam_nivel,
				ctam_salto_pag,
				ctam_ajustable,
				ctam_ley_debe1,
				ctam_ley_debe2,
				ctam_ley_haber1,
				ctam_ley_haber2,
				ctam_rubro,
				ctam_fl_ccosto,
				ctam_cuenta_alfa,
				ctam_aju_mon_ext,
				ctam_cta_dif_cbio
            ' , 
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' " 
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		$usuario_id = Auth::user()->id;

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];

			switch($data->ctam_tipo)
			{
			case '0':
				$tipocuenta = '1';
				break;
			case '1':
			case '3':
				$titulo_id = 0;
				$nivel++;
				$tipocuenta = '2';
				break;
			default:
				$tipocuenta = '3';
		  	}

            $cuentacontable = Cuentacontable::create([
                "empresa_id" => $data->ctam_empresa,
                "rubrocontable_id" => $data->ctam_rubro,
                "cuentacontable_id" => $titulo_id,
                "orden" => $id+1,
				"nivel" => $nivel,
                "nombre" => $data->ctam_desc,
                "codigo" => $data->ctam_cuenta,
                "tipocuenta" => $tipocuenta,
                "monetaria" => ($data->ctam_ajustable == 'S' ? '1' : '2'),
                "manejccosto" => ($data->ctam_fl_ccosto == 'S' ? '1' : '2'),
				"usuarioultcambio_id" => $usuario_id
            ]);

			if ($data->ctam_tipo == '1' || $data->ctam_tipo == '3')
				$titulo_id = $cuentacontable->id;
			$id = $cuentacontable->id;

			if ($data->ctam_tipo == '2')
				$nivel--;
        }
    }

	public function guardarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		cambia_para_grabar($request, $codigo, $tipocuenta, $ajustable, $manejaccosto, $cuenta);

        $data = array( 'tabla' => $this->tableAnita, 
						'acc' => 'insert',
            			'campos' => ' ctam_empresa, ctam_cuenta, ctam_tipo, ctam_desc, ctam_nivel, ctam_salto_pag, ctam_ajustable, ctam_ley_debe1, ctam_ley_debe2, ctam_ley_haber1, ctam_ley_haber2, ctam_rubro, ctam_fl_ccosto, ctam_cuenta_alta, ctam_aju_mon_ext, ctam_cta_dif_cbio',
            			'valores' => " '".$request->empresa_id."', '".$codigo."', '".$tipocuenta."', '".$request->nombre."', '".$request->nivel."', '".'N'.", ".$ajustable."', '".' '."', ".' '.", '".' '."', '".' '."', '".$request->rubrocontable_id."', '".$manejaccosto."', '".$cuenta."', '".'N'.", '".'0'."' "
        );
        $apiAnita->apiCall($data);
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();

		cambia_para_grabar($request, $codigo, $tipocuenta, $ajustable, $manejaccosto, $cuenta);

		$data = array( 'acc' => 'update', 
						'tabla' => $this->tableAnita, 
            			'valores' => " ctam_empresa = '".$request->empresa_id."', ctam_cuenta = '".$codigo."', ctam_tipo = '".$tipocuenta."', ctam_desc = '".$request->nombre."', ctam_nivel = '".$request->nivel."', ctam_ajustable = '".$ajustable."', ctam_rubro ='".$request->rubro."', ctam_fl_ccosto = '".$manejaccosto."', ctam_cuenta_alfa ='".$cuenta."' ",
						'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}

	public function eliminarAnita($id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita,
					'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id."' " );
        $apiAnita->apiCall($data);
	}

	public function cambia_para_grabar($request, &$codigo, &$tipocuenta, &$ajustable, &$manejaccosto, &$cuenta)
	{
		switch($request->tipocuenta)
		{
		case '1':
			$tipocuenta = '0';
			break;
		case '2':
			$tipocuenta = '1';
			break;
		default:
			$tipocuenta = '2';
		}

		if ($request->ajustable == '1')
			$ajustable = 'S';
		else
			$ajustable = 'N';

		if ($request->manejaccosto == '1')
			$manejaccosto = 'S';
		else
			$manejaccosto = 'N';

		// Convierte a formato cuenta de anita
		sprintf($codigo, "%09ld", $request->codigo);
		$cuenta = substr($codigo,0,6).'-'.substr($codigo,-3);
	}
}

