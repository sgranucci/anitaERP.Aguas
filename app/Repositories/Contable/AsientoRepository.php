<?php

namespace App\Repositories\Contable;

use App\Models\Contable\Asiento;
use App\Repositories\Contable\Asiento_MovimientoRepositoryInterface;
use App\Repositories\Contable\CuentacontableRepositoryInterface;
use App\Repositories\Contable\CentrocostoRepositoryInterface;
use App\Repositories\Contable\TipoasientoRepositoryInterface;
use App\Repositories\Configuracion\MonedaRepositoryInterface;
use App\Repositories\Configuracion\EmpresaRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\ApiAnita;
use Carbon\Carbon;
use Auth;
use DB;

class AsientoRepository implements AsientoRepositoryInterface
{
    protected $model;
    protected $tableAnita = ['ctamov', 'subdiario'];
    protected $keyField = 'numeroasiento';
    protected $keyFieldAnita = ['ctav_empresa', 'ctav_nro_asiento', 'ctav_nro_linea'];

	private $centrocostoRepository;
	private $asiento_movimientoRepository;
	private $monedaRepository;
	private $empresaRepository;
	private $cuentacontableRepository;
	private $tipoasientoRepository;
	private $flGrabaAsiento, $numeroAsientoActual;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Asiento $asiento,
								CentrocostoRepositoryInterface $centrocostorepository,
								Asiento_MovimientoRepositoryInterface $asiento_movimientorepository,
								MonedaRepositoryInterface $monedarepository,
								EmpresaRepositoryInterface $empresarepository,
								TipoasientoRepositoryInterface $tipoasientorepository,
								CuentacontableRepositoryInterface $cuentacontablerepository
								)
    {
        $this->model = $asiento;
		$this->centrocostoRepository = $centrocostorepository;
		$this->asiento_movimientoRepository= $asiento_movimientorepository;
		$this->monedaRepository = $monedarepository;
		$this->empresaRepository = $empresarepository;
		$this->tipoasientoRepository = $tipoasientorepository;
		$this->cuentacontableRepository = $cuentacontablerepository;
    }

    public function create(array $data)
    {
		$data['numeroasiento'] = self::ultimoAsiento($data['empresa_id']);
		$data['usuario_id'] = Auth::user()->id;

		$asiento = $this->model->create($data);

		// Graba anita
		//$anita = self::guardarAnita($data);
		
		// Actualiza anita asi borra el asiento anterior por si ya existe
		$anita = self::actualizarAnita($data);

		if (strpos($anita, 'Error') !== false)
			throw new Exception($anita);

		return $asiento;
    }

    public function update(array $data, $id)
    {
		$data['usuario_id'] = Auth::user()->id;

        $asiento = $this->model->findOrFail($id)->update($data);

		// Actualiza anita
		$anita = self::actualizarAnita($data);

		if (strpos($anita, 'Error') !== false)
			throw new Exception($anita);

		return $asiento;
    }

    public function delete($id)
    {
    	$asiento = Asiento::find($id);

		// Elimina anita
		if ($asiento)
		{
			$empresa = $this->empresaRepository->findPorId($asiento->empresa_id);
			if ($empresa)
				$codigoEmpresa = $empresa->codigo;
			else
				$codigoEmpresa = 1;
						
			$anita = self::eliminarAnita($codigoEmpresa, $asiento->numeroasiento);

			if (strpos($anita, 'Error') !== false)
				return 'Error';

        	$asiento = $this->model->destroy($id);
		}

		return $asiento;
    }

    public function find($id)
    {
        if (null == $asiento = $this->model->with("asiento_movimientos")
									->with("asiento_archivos")
									->with("tipoasientos")
									->with("empresas")
									->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $asiento;
    }

    public function findOrFail($id)
    {
        if (null == $asiento = $this->model->with("asiento_movimientos")
											->with("asiento_archivos")
											->with("tipoasientos")
											->with("empresas")
											->findOrFail($id))
			{
            throw new ModelNotFoundException("Registro no encontrado");
        }
        return $asiento;
    }

	public function leeAsientoPorClave($id, $clave)
	{
		return $this->model->where($clave, $id)
							->with("asiento_movimientos")
							->with("asiento_archivos")
							->with("tipoasientos")
							->with("empresas")
							->get();
	}

    public function sincronizarConAnita(){
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
		
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 
						'sistema' => 'contab',
						'campos' => $this->keyFieldAnita[0].",".$this->keyFieldAnita[1].",".$this->keyFieldAnita[2], 
						'tabla' => $this->tableAnita[0] );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		$this->flGrabaAsiento = true;
		$this->numeroAsientoActual = 0;
        foreach ($dataAnita as $value) {
            $this->traerRegistroDeAnita($value->{$this->keyFieldAnita[0]},
										$value->{$this->keyFieldAnita[1]}, 
										$value->{$this->keyFieldAnita[2]});
        }

		$dataAnita = DB::table('anitasubdiario')
		->select('subd_nro_operacion',
				 'subd_fecha',
				 'subd_cod_mon')
		->orderBy('subd_nro_operacion')
		->get();

		$this->flGrabaAsiento = true;
		foreach($dataAnita as $value)
        	$this->traerRegistroDeAnitaSubdiario($value->subd_nro_operacion, $value->subd_fecha, $value->subd_cod_mon);
    }

    private function traerRegistroDeAnita($empresa, $asiento, $linea){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita[0], 
			'sistema' => 'contab',
            'campos' => '
					ctav_empresa,
					ctav_nro_asiento,
					ctav_nro_linea,
					ctav_d_h,
					ctav_cuenta,
					ctav_fecha,
					ctav_tipo,
					ctav_letra,
					ctav_sucursal ,
					ctav_nro,
					ctav_importe,
					ctav_desc_mov,
					ctav_cotizacion,
					ctav_cod_mon,
					ctav_sistema,
					ctav_balancea,
					ctav_tipo_asiento,
					ctav_asi_mon_ref,
					ctav_ccosto,
					ctav_usuario_umod,
					ctav_fecha_umod,
					ctav_hora_umod,
					ctav_o_compra 
			',
            'whereArmado' => " WHERE ".$this->keyFieldAnita[0]." = '".$empresa."' AND ".
									   $this->keyFieldAnita[1]." = '".$asiento."' AND ".
									   $this->keyFieldAnita[2]." = '".$linea."' "
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

		$usuario_id = Auth::user()->id;

        if (isset($dataAnita)) {
            $data = $dataAnita[0];

			if ($data->ctav_nro_asiento != $this->numeroAsientoActual)
			{
				$this->numeroAsientoActual = $data->ctav_nro_asiento;
				$this->flGrabaAsiento = true;
			}

			$empresa = $this->empresaRepository->findPorCodigo($data->ctav_empresa);
			if ($empresa)
				$empresa_id = $empresa->id;
			else
				$empresa_id = 1;
						
			$cuenta = $this->cuentacontableRepository->findPorCodigo($data->ctav_empresa, $data->ctav_cuenta);
			if ($cuenta)
				$cuentacontable_id = $cuenta->id;
			else
				$cuentacontable_id = NULL;

			$centrocosto = $this->centrocostoRepository->findPorCodigo($data->ctav_ccosto);
			if ($centrocosto)
				$centrocosto_id = $centrocosto->id;
			else
				$centrocosto_id = 1;

			$moneda = $this->monedaRepository->findPorCodigo($data->ctav_cod_mon);
			if ($moneda)
				$moneda_id = $moneda->id;
			else
				$moneda_id = NULL;
	
			if ($this->flGrabaAsiento)
			{
				$observacion = $data->ctav_sistema.' '.$data->ctav_tipo.' '.$data->ctav_letra.' '.
								$data->ctav_sucursal.' '.$data->ctav_nro;

				if ($data->ctav_tipo_asiento === '   ')
					$tipoasiento_id = 1;
				else
				{
					$tipoasiento = $this->tipoasientoRepository->findPorAbreviatura($data->ctav_tipo_asiento);
					if ($tipoasiento)
						$tipoasiento_id = $tipoasiento->id;
					else
						$tipoasiento_id = 1;
		
				}
				$arr_campos = [
					'empresa_id' => $empresa_id,
					'tipoasiento_id' => $tipoasiento_id,
					'numeroasiento' => $data->ctav_nro_asiento,
					'fecha' => $data->ctav_fecha,
					'venta_id' => null,
					'movimientostock_id' => null,
					'compra_id' => null,
					'caja_movimiento_id' => null,
					'ordencompra_id' => $data->ctav_o_compra,
					'recepcionproveedor_id' => null,
					'observacion' => $observacion,
					'usuario_id' => $usuario_id,
					];
		
				$asiento = $this->model->create($arr_campos);

				$this->flGrabaAsiento = false;
			}

			// Graba tabla de movimientos de asientos
			if ($cuentacontable_id != NULL)
			{
				$arr_asimov = [
					'asiento_id' => $asiento->id,
					'cuentacontable_id' => $cuentacontable_id, 
					'centrocosto_id' => $centrocosto_id, 
					'monto' => ($data->ctav_d_h == 'D' ? $data->ctav_importe : -$data->ctav_importe), 
					'moneda_id' => $moneda_id,
					'cotizacion' => $data->ctav_cotizacion, 
					'observacion' => $data->ctav_desc_mov
				];
				$this->asiento_movimientoRepository->createUnique($arr_asimov);
			}
        }
    }

	private function traerRegistroDeAnitaSubdiario($numeroOperacion, $fecha, $cod_mon) {
		$dataAnita = DB::table('anitasubdiario')
		->select(
				'subd_sistema',
				'subd_fecha',
				'subd_tipo',
				'subd_letra',
				'subd_sucursal',
				'subd_nro',
				'subd_emisor',
				'subd_tipo_mov',
				'subd_cuenta',
				'subd_contrapartida',
				'subd_nro_operacion',
				'subd_ref_tipo',
				'subd_ref_letra',
				'subd_ref_sucursal',
				'subd_ref_nro',
				'subd_ref_sistema',
				'subd_importe',
				'subd_cod_mon',
				'subd_cotizacion',
				'subd_desc_mov',
				'subd_nro_asiento',
				'subd_procesado',
				'subd_ccosto_cta',
				'subd_ccosto_con',
				'subd_nro_interno',
				'subd_empresa',
				'subd_usuario',
				'subd_fecha_ult_act',
				'subd_hora_ult_act')
		->where('subd_nro_operacion', $numeroOperacion)
		->where('subd_fecha', $fecha)
		->where('subd_cod_mon', $cod_mon)
		->get();

		$usuario_id = Auth::user()->id;
		$this->numeroAsientoActual = 0;

		foreach ($dataAnita as $data) {
			if ($data->subd_nro_operacion != $this->numeroAsientoActual)
			{
				$this->numeroAsientoActual = $data->subd_nro_operacion;
				$this->flGrabaAsiento = true;
			}

			$empresa = $this->empresaRepository->findPorCodigo($data->subd_empresa);
			if ($empresa)
				$empresa_id = $empresa->id;
			else
				$empresa_id = 1;
						
			$cuenta = $this->cuentacontableRepository->findPorCodigo($data->subd_empresa, $data->subd_cuenta);
			if ($cuenta)
				$cuentacontable_id = $cuenta->id;
			else
				$cuentacontable_id = NULL;

			$centrocosto = $this->centrocostoRepository->findPorCodigo($data->subd_ccosto_cta);
			if ($centrocosto)
				$centrocosto_id = $centrocosto->id;
			else
				$centrocosto_id = 1;

			$moneda = $this->monedaRepository->findPorCodigo($data->subd_cod_mon);
			if ($moneda)
				$moneda_id = $moneda->id;
			else
				$moneda_id = NULL;
	
			if ($this->flGrabaAsiento)
			{
				$observacion = $data->subd_sistema.' '.$data->subd_tipo.' '.$data->subd_letra.' '.
								$data->subd_sucursal.' '.$data->subd_nro;

				switch($data->subd_sistema)
				{
					case 'V':
						$tipoasiento_id = 7;
						break;
					case 'C':
						$tipoasiento_id = 8;
						break;
					case 'T':
						$tipoasiento_id = 9;
						break;
					case 'S':
						$tipoasiento_id = 10;
						break;
				}
				$arr_campos = [
					'empresa_id' => $empresa_id,
					'tipoasiento_id' => $tipoasiento_id,
					'numeroasiento' => $data->subd_nro_operacion,
					'fecha' => $data->subd_fecha,
					'venta_id' => null,
					'movimientostock_id' => null,
					'compra_id' => null,
					'caja_movimiento_id' => null,
					'ordencompra_id' => null,
					'recepcionproveedor_id' => null,
					'observacion' => $observacion,
					'usuario_id' => $usuario_id,
					];
		
				$asiento = $this->model->create($arr_campos);

				$this->flGrabaAsiento = false;
			}

			// Graba tabla de movimientos de asientos
			if ($cuentacontable_id != NULL)
			{
				$arr_asimov = [
					'asiento_id' => $asiento->id,
					'cuentacontable_id' => $cuentacontable_id, 
					'centrocosto_id' => $centrocosto_id, 
					'monto' => ($data->subd_tipo_mov == 'D' ? $data->subd_importe : -$data->subd_importe), 
					'moneda_id' => $moneda_id,
					'cotizacion' => $data->subd_cotizacion, 
					'observacion' => $data->subd_desc_mov
				];
				$this->asiento_movimientoRepository->createUnique($arr_asimov);
			}

			// Genera contrapartida
			$cuenta = $this->cuentacontableRepository->findPorCodigo($data->subd_empresa, $data->subd_contrapartida);
			if ($cuenta)
				$cuentacontable_id = $cuenta->id;
			else
				$cuentacontable_id = NULL;
	
			$centrocosto = $this->centrocostoRepository->findPorCodigo($data->subd_ccosto_con);
			if ($centrocosto)
				$centrocosto_id = $centrocosto->id;
			else
				$centrocosto_id = 1;

			// Graba tabla de movimientos de asientos
			if ($cuentacontable_id != NULL)
			{
				$arr_asimov = [
					'asiento_id' => $asiento->id,
					'cuentacontable_id' => $cuentacontable_id, 
					'centrocosto_id' => $centrocosto_id, 
					// Si va al debe la contrapartida va al haber
					'monto' => ($data->subd_tipo_mov == 'D' ? -$data->subd_importe : $data->subd_importe), 
					'moneda_id' => $moneda_id,
					'cotizacion' => $data->subd_cotizacion, 
					'observacion' => $data->subd_desc_mov
				];
				$this->asiento_movimientoRepository->createUnique($arr_asimov);
			}
        }
    }

	private function guardarAnita($request) 
	{
		// Graba asiento
		if (isset($request['cuentacontable_ids']))
		{
			$apiAnita = new ApiAnita();

			$centrocostos = $request['centrocosto_ids'];
			$debes = $request['debes'];
			$haberes = $request['haberes'];
			$cuentacontables = $request['cuentacontable_ids'];
			$observaciones = $request['observaciones'];
			$moneda_ids = $request['moneda_ids'];
			$cotizaciones = $request['cotizaciones'];
			
			$fecha = Carbon::createFromFormat( 'Y-m-d', $request['fecha'])->format('Ymd');

			$empresa = $this->empresaRepository->findPorId($request['empresa_id']);
			
			if ($empresa)
				$codigoEmpresa = $empresa->codigo;
			else
				$codigoEmpresa = 1;

			$tipoasiento = $this->tipoasientoRepository->find($request['tipoasiento_id']);
			if ($tipoasiento)
				$codigoTipoAsiento = $tipoasiento->abreviatura;
			else
				$codigoTipoAsiento = 1;

			$sistema = 'B';
			$tipo = $letra = ' ';
			$sucursal = $nro = 0;

			if ($cuentacontables[0] != null)
				$qMovimiento = count($cuentacontables);
			else
				$qMovimiento = 0;
			
			for ($i_movimiento=0; $i_movimiento < $qMovimiento; $i_movimiento++) 
			{
				$observacion = preg_replace('([^A-Za-z0-9 ])', '', $observaciones[$i_movimiento]);

				if ($debes[$i_movimiento] != 0)
				{
					$d_h = 'D';
					$monto = $debes[$i_movimiento];
				}

				if ($haberes[$i_movimiento] != 0)
				{
					$d_h = 'H';
					$monto = abs($haberes[$i_movimiento]);
				}
				$cuenta = $this->cuentacontableRepository->findPorId($cuentacontables[$i_movimiento]);
				if ($cuenta)
					$cuentacontable = $cuenta->codigo;
				else
					$cuentacontable = NULL;
				$centrocosto = $this->centrocostoRepository->findPorId($centrocostos[$i_movimiento]);
				if ($centrocosto)
					$codigoCentroCosto = $centrocosto->codigo;
				else
					$codigoCentroCosto = 0;
				$moneda = $this->monedaRepository->findPorCodigo($moneda_ids[$i_movimiento]);
				if ($moneda)
					$codigoMoneda = $moneda->codigo;
				else
					$codigoMoneda = '1';
				$data = array( 'tabla' => $this->tableAnita[0], 
						'acc' => 'insert',
						'sistema' => 'contab',
						'campos' => '
							ctav_empresa,
							ctav_nro_asiento,
							ctav_nro_linea,
							ctav_d_h,
							ctav_cuenta,
							ctav_fecha,
							ctav_tipo,
							ctav_letra,
							ctav_sucursal ,
							ctav_nro,
							ctav_importe,
							ctav_desc_mov,
							ctav_cotizacion,
							ctav_cod_mon,
							ctav_sistema,
							ctav_balancea,
							ctav_tipo_asiento,
							ctav_asi_mon_ref,
							ctav_ccosto,
							ctav_usuario_umod,
							ctav_fecha_umod,
							ctav_hora_umod,
							ctav_o_compra 
						',
						'valores' => " 
						'".$codigoEmpresa."', 
						'".$request['numeroasiento']."',
						'".$i_movimiento."',
						'".$d_h."',
						'".$cuentacontable."',
						'".$fecha."',
						'".$tipo."',
						'".$letra."',
						'".$sucursal."',
						'".$nro."',
						'".abs($monto)."',
						'".$observacion."',
						'".$cotizaciones[$i_movimiento]."',
						'".$codigoMoneda."',
						'".$sistema."',
						'".'S'."',
						'".$codigoTipoAsiento."',
						'".'0'."',
						'".$codigoCentroCosto."',
						'".' '."',
						'".'0'."',
						'".' '."',
						".'0'." "
      			);
        		$asiento = $apiAnita->apiCall($data);
				if (strpos($asiento, 'Error') !== false)
					return 'Error grabación ctamov anita';
			}
		}
		return 'Success';
	}

	private function actualizarAnita($request) 
	{
		$empresa = $this->empresaRepository->findPorId($request['empresa_id']);
		if ($empresa)
			$codigoEmpresa = $empresa->codigo;
		else
			$codigoEmpresa = 1;

		// Borra asiento
		$apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita[0], 
				'sistema' => 'contab',
				'whereArmado' => " WHERE ctav_empresa = '".$codigoEmpresa."' and ctav_nro_asiento = '".
									$request['numeroasiento']."' ");
        $apiAnita->apiCall($data);

		// Crea el asiento
		$asiento = Self::guardarAnita($request);

		if (strpos($asiento, 'Error') !== false)
			return 'Error grabación ctamov anita';	

		return 'Success';
	}

	private function eliminarAnita($empresa, $codigo) 
	{
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita[0], 
				'sistema' => 'contab',
				'whereArmado' => " WHERE ctav_empresa = '".$empresa."' and ctav_nro_asiento = '".$codigo."' ");
        $apiAnita->apiCall($data);
	}

	// Devuelve ultimo codigo de asientos + 1 para agregar nuevos en Anita

	private function ultimoAsiento($empresa_id) 
	{
		$asiento = $this->model->select('numeroasiento')->where('empresa_id', $empresa_id)->orderBy('id', 'desc')->first();
		
		$numeroasiento = 0;
        if ($asiento) 
		{
			$numeroasiento = $asiento->numeroasiento;
			$numeroasiento = $numeroasiento + 1;
		}
		else	
			$numeroasiento = 1;

		return $numeroasiento;
	}
}
