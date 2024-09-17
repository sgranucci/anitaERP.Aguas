<?php

namespace App\Repositories\Contable;

use App\Models\Contable\Cuentacontable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Auth;

class CuentacontableRepository implements CuentacontableRepositoryInterface
{
    protected $model;
    protected $tableAnita = 'ctamae';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = 'ctam_cuenta';

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Cuentacontable $cuentacontable)
    {
        $this->model = $cuentacontable;
    }

    public function all()
    {
        $hay_cuentacontable = Cuentacontable::first();

		if (!$hay_cuentacontable)
			self::sincronizarConAnita();

        return $this->model->with('empresas')->with('rubrocontables')->orderBy('nombre','ASC')->get();
    }

    public function create(array $data)
    {
        $cuentacontable = $this->model->create($data);
		//
		// Graba anita
		self::guardarAnita($data);
    }

    public function update(array $data, $id)
    {
        $cuentacontable = $this->model->findOrFail($id)
            ->update($data);

        // Actualiza anita
		self::actualizarAnita($data, $data['codigo']);

		return $cuentacontable;
    }

    public function delete($id)
    {
    	$cuentacontable = $this->model->find($id);
		//
		// Elimina anita
		self::eliminarAnita($cuentacontable->codigo);

        $cuentacontable = $this->model->destroy($id);

		return $cuentacontable;
    }

    public function find($id)
    {
        if (null == $cuentacontable = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cuentacontable;
    }

    public function findPorId($id)
    {
        $cuentacontable = $this->model->where('id', $id)->first();

        return $cuentacontable;
    }

    public function findPorCodigo($codigo)
    {
        $cuentacontable = $this->model->where('codigo', $codigo)->first();

        return $cuentacontable;
    }

    public function findOrFail($id)
    {
        if (null == $cuentacontable = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cuentacontable;
    }

    public function sincronizarConAnita(){
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 'sistema' => 'contab',
                        'campos' => 'ctam_empresa, '.$this->keyFieldAnita, 
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
                	$this->traerRegistroDeAnita($value->ctam_empresa, $value->{$this->keyFieldAnita});
            	}
        	}
		}
    }

    public function traerRegistroDeAnita($empresa, $key){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'sistema' => 'contab',
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
            'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$key."' AND ctam_empresa = '".$empresa."'" 
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
				$tipocuenta = '2';
				break;
			default:
				$tipocuenta = '3';
		  	}

            // Lee el concepto de gasto        
            $apiAnitaConc = new ApiAnita();
            $dataConc = array( 
                'acc' => 'list', 'tabla' => 'ctaconc', 
                'sistema' => 'contab',
                'campos' => '
                    ctaco_empresa,
                    ctaco_cuenta,
                    ctaco_concepto
                ' , 
                'whereArmado' => " WHERE ctaco_cuenta = '".$key."' AND ctaco_empresa = '".$empresa."' " 
            );
            $dataAnitaConc = json_decode($apiAnita->apiCall($dataConc));

            $conceptogasto_id = null;
            if (count($dataAnitaConc) > 0)
            {
                // Busca concepto por codigo
                $conceptogasto = Conceptogasto::find($dataAnitaConc);

                if ($conceptogasto)
                    $conceptogasto_id = $conceptogasto->id;
                else    
                    $conceptogasto_id = null;
            }

            $cuentacontable = Self::create([
                "empresa_id" => $data->ctam_empresa,
                "rubrocontable_id" => $data->ctam_rubro,
				"nivel" => $data->ctam_nivel,
                "nombre" => $data->ctam_desc,
                "codigo" => $data->ctam_cuenta,
                "tipocuenta" => $tipocuenta,
                "monetaria" => ($data->ctam_ajustable == 'S' ? '1' : '2'),
                "manejaccosto" => ($data->ctam_fl_ccosto == 'S' ? '1' : '2'),
				"usuarioultcambio_id" => $usuario_id,
                "ajustamonedaextranjera" => $data->ctam_aju_mon_ext,
                "conceptogasto_id" => $conceptogasto_id,
                "cuentacontable_difcambio_id" => $data->ctam_cta_dif_cbio
            ]);
        }
    }

	public function guardarAnita($request) {
        $apiAnita = new ApiAnita();

		Self::cambia_para_grabar($request, $codigo, $tipocuenta, $ajustable, $manejaccosto, $cuenta,
                            $cuentacontable_difcambio);

        $data = array( 'tabla' => $this->tableAnita, 
                        'sistema' => 'contab',
						'acc' => 'insert',
            			'campos' => ' ctam_empresa, ctam_cuenta, ctam_tipo, ctam_desc, ctam_nivel, 
                                        ctam_salto_pag, ctam_ajustable, ctam_ley_debe1, ctam_ley_debe2, 
                                        ctam_ley_haber1, ctam_ley_haber2, ctam_rubro, ctam_fl_ccosto, 
                                        ctam_cuenta_alfa, ctam_aju_mon_ext, ctam_cta_dif_cbio',
            			'valores' => 
                                " '".$request['empresa_id']."', 
                                '".$codigo."', 
                                '".$tipocuenta."', 
                                '".$request['nombre']."', 
                                '".$request['nivel']."', 
                                '".'N'.", 
                                ".$ajustable."', 
                                '".' '."', 
                                '".' '."', 
                                '".' '."', 
                                '".' '."', 
                                '".$request['rubrocontable_id']."', 
                                '".$manejaccosto."', 
                                '".$cuenta."', 
                                '".$request['ajustamonedaextranjera'].", 
                                '".$cuentacontable_difcambio."' "
        );
        $apiAnita->apiCall($data);

        // Lee el concepto de gasto        
        $apiAnitaConc = new ApiAnita();
        $data = array( 
            'acc' => 'insert', 
            'tabla' => 'ctaconc', 
            'sistema' => 'contab',
            'campos' => '
                ctaco_empresa,
                ctaco_cuenta,
                ctaco_concepto
            ' , 
            'valores' => 
                " '".$request['empresa_id']."', 
                '".$codigo."', 
                '".$request['conceptogasto_id']."' ",
            'whereArmado' => " WHERE ctaco_cuenta = '".$codigo.
                            "' AND ctaco_empresa = '".$request['empresa_id']."' " 
        );
        $dataAnitaConc = json_decode($apiAnitaConc->apiCall($data));
	}

	public function actualizarAnita($request) {
        $apiAnita = new ApiAnita();

        Self::cambia_para_grabar($request, $codigo, $tipocuenta, $ajustable, $manejaccosto, $cuenta,
                            $cuentacontable_difcambio);

        $data = array( 'acc' => 'update', 
                        'sistema' => 'contab',
						'tabla' => $this->tableAnita, 
            			'valores' => "
                            ctam_empresa = '".$request['empresa_id']."', 
                            ctam_cuenta = '".$codigo."', 
                            ctam_tipo = '".$tipocuenta."', 
                            ctam_desc = '".$request['nombre']."', 
                            ctam_nivel = '".$request['nivel']."', 
                            ctam_ajustable = '".$ajustable."', 
                            ctam_rubro ='".$request['rubrocontable_id']."', 
                            ctam_fl_ccosto = '".$manejaccosto."', 
                            ctam_cuenta_alfa = '".$cuenta."',
                            ctam_aju_mon_ext = '".$request['ajustamonedaextranjera']."',
                            ctam_cta_dif_cbio = '".$cuentacontable_difcambio."'",
						'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$codigo.
                                            "' AND ctam_empresa='".$request['empresa_id']."' ");
        $apiAnita->apiCall($data);

        $apiAnitaConc = new ApiAnita();

		$data = array( 'acc' => 'update', 
                        'sistema' => 'contab',
						'tabla' => 'ctaconc', 
            			'valores' => "
                            ctaco_empresa = '".$request['empresa_id']."', 
                            ctaco_cuenta = '".$codigo."', 
                            ctaco_concepto = '".$request['conceptogasto_id']."' ",
						'whereArmado' => " WHERE ctaco_cuenta = '".$codigo.
                                            "' AND ctaco_empresa='".$request['empresa_id']."' ");
        $apiAnitaConc->apiCall($data);
	}

	public function eliminarAnita($empresa, $id) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita,
                    'sistema' => 'contab',
					'whereArmado' => " WHERE ".$this->keyFieldAnita." = '".$id.
                    "' AND ctam_empresa = '".$empresa."'" );
        $apiAnita->apiCall($data);

        $apiAnitaConc = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita,
                    'sistema' => 'contab',
					'whereArmado' => " WHERE ctaco_cuenta = '".$id.
                    "' AND ctaco_empresa = '".$empresa."'");
        $apiAnitaConc->apiCall($data);
	}

	public function cambia_para_grabar($request, &$codigo, &$tipocuenta, &$ajustable, 
                                        &$manejaccosto, &$cuenta, &$cuentacontable_difcambio)
	{
		switch($request['tipocuenta'])
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

        if ($request['monetaria'] == '1')
			$ajustable = 'S';
		else
			$ajustable = 'N';

		if ($request['manejaccosto'] == '1')
			$manejaccosto = 'S';
		else
			$manejaccosto = 'N';

		// Convierte a formato cuenta de anita
		sprintf($codigo, "%09ld", $request['codigo']);
		$cuenta = substr($codigo,0,6).'-'.substr($codigo,-3);

        // Busca cuenta contable de diferencia de cambio
        $cuentacontable_difcambio = '0';
        if ($request['cuentacontable_difcambio_id'])
        {
            $cuentacontable = Self::find($request['cuentacontable_difcambio_id']);
            if ($cuentacontable)
                $cuentacontable_difcambio = $cuentacontable->codigo;
            else
                $cuentacontable_difcambio = '0';
        }
	}
}
