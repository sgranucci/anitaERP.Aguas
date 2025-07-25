<?php

namespace App\Repositories\Caja;

use App\Models\Caja\Cheque;
use App\Models\Caja\Cuentacaja;
use App\Models\Contable\Cuentacontable;
use App\Models\Configuracion\Empresa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Caja\BancoRepositoryInterface;
use App\Repositories\Caja\CuentacajaRepositoryInterface;
use App\Repositories\Caja\Estadocheque_BancoRepositoryInterface;
use App\Repositories\Caja\ChequeraRepositoryInterface;
use App\Repositories\Compras\ProveedorRepositoryInterface;
use App\Repositories\Configuracion\EmpresaRepositoryInterface;
use App\Repositories\Configuracion\TipodocumentoRepositoryInterface;
use App\ApiAnita;
use Auth;
use DB;
use Carbon\Carbon;
use Exception;

class ChequeRepository implements ChequeRepositoryInterface
{
    protected $model;
    protected $tableAnita = 'cpromae';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = ['cpro_cuenta', 'cpro_nro_cheque', 'cpro_fecha_cheque'];

	private $bancoRepository;
    private $cuentacajaRepository;
    private $proveedorRepository;
    private $empresaRepository;
    private $tipodocumentoRepository;
    private $estadocheque_bancoRepository;
    private $chequeraRepository;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Cheque $cheque,
                                CuentacajaRepositoryInterface $cuentacajarepository,
                                ProveedorRepositoryInterface $proveedorrepository,
                                EmpresaRepositoryInterface $empresarepository,
                                ChequeraRepositoryInterface $chequerarepository,
                                TipodocumentoRepositoryInterface $tipodocumentorepository,
                                BancoRepositoryInterface $bancorepository,
                                Estadocheque_BancoRepositoryInterface $estadocheque_bancorepository)
    {
        $this->model = $cheque;
        $this->cuentacajaRepository = $cuentacajarepository;
        $this->proveedorRepository = $proveedorrepository;
        $this->empresaRepository = $empresarepository;
        $this->chequeraRepository = $chequerarepository;
        $this->tipodocumentoRepository = $tipodocumentorepository;
        $this->bancoRepository = $bancorepository;
        $this->estadocheque_bancoRepository = $estadocheque_bancorepository;
    }

    public function all()
    {
        $hay_cheque = Cheque::first();

        if (!$hay_cheque)
            self::sincronizarConAnita();

        return $this->model->with('empresas')
                            ->with('cuentacajas')
                            ->with('bancos')
                            ->with('tipodocumentos')
                            ->with('proveedores')
                            ->with('clientes')
                            ->with('monedas')
                            ->with('cajas')
                            ->with('chequeras')
                            ->get();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try 
        {
            $cheque = $this->model->create($data);

            // Graba anita
		    $anita = self::guardarAnita($data);

            if (strpos($anita, 'Error') !== false)
                throw new Exception('No pudo grabar ANITA');

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            dd($e->getMessage());

            return ['error' => $e->getMessage()];
        }
        return($cheque);
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try 
        {
            $cheque = $this->model->findOrFail($id)->update($data);

            // Actualiza anita
		    $anita = self::actualizarAnita($data, $data['codigo']);

            if (strpos($anita, 'Error') !== false)
                throw new Exception('No pudo grabar ANITA');

            DB::commit();

        } catch (\Exception $e) {
            
            DB::rollback();

            dd($e->getMessage());
            return ['error' => $e->getMessage()];
        }
        return($cheque);
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try 
        {
    	    $cheque = $this->model->find($id);
        		
		    // Elimina anita
		    $anita = self::eliminarAnita($cheque->codigo);

            $cheque = $this->model->destroy($id);

            if (strpos($anita, 'Error') !== false)
                throw new Exception('No pudo grabar ANITA');

            DB::commit();   

        } catch (\Exception $e) {
            
            DB::rollback();

            return ['error' => $e->getMessage()];
        }
		return $cheque;
    }

    public function find($id)
    {
        if (null == $cheque = $this->model->with('empresas')
                            ->with('cuentacajas')
                            ->with('bancos')
                            ->with('tipodocumentos')
                            ->with('proveedores')
                            ->with('clientes')
                            ->with('monedas')
                            ->with('cajas')
                            ->with('chequeras')
                            ->with('caja_movimientos')
                            ->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cheque;
    }

    public function findOrFail($id)
    {
        if (null == $cheque = $this->model->with('empresas')
                            ->with('cuentacajas')
                            ->with('bancos')
                            ->with('tipodocumentos')
                            ->with('proveedores')
                            ->with('clientes')
                            ->with('monedas')
                            ->with('cajas')
                            ->with('chequeras')
                            ->with('caja_movimientos')
                            ->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cheque;
    }

    public function findPorNumeroCheque($codigo)
    {
        return $this->model->where('numerocheque', $codigo)->with('empresas')
                            ->with('cuentacajas')
                            ->with('bancos')
                            ->with('tipodocumentos')
                            ->with('proveedores')
                            ->with('clientes')
                            ->with('monedas')
                            ->with('chequeras')
                            ->with('caja_movimientos')
                            ->with('cajas')->get();
    }

    public function sincronizarConAnita(){
		ini_set('max_execution_time', '300');

        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 
						'sistema' => 'che_ban',
						'campos' => $this->keyFieldAnita[0].','.$this->keyFieldAnita[1].','.$this->keyFieldAnita[2], 
						'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        foreach ($dataAnita as $value) {
            $this->traerRegistroDeAnita($value->{$this->keyFieldAnita[0]}, $value->{$this->keyFieldAnita[1]}, $value->{$this->keyFieldAnita[2]});
        }
    }

    public function traerRegistroDeAnita($key1, $key2, $key3){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
			'sistema' => 'che_ban',
            'campos' => '
                    cpro_cuenta,
                    cpro_nro_cheque,
                    cpro_fecha_cheque,
                    cpro_fecha_emision,
                    cpro_importe,
                    cpro_proveedor,
                    cpro_entregado_a,
                    cpro_nro_op,
                    cpro_cod_mon,
                    cpro_cotizacion,
                    cpro_estado,
                    cpro_contrapartida,
                    cpro_fecha_anula,
                    cpro_fl_imprimio,
                    cpro_a_nombre_de,
                    cpro_modelo,
                    cpro_para_dep',
                    //,
                    //cpro_fecha_entrega,
                    //cpro_empresa,
                    //cpro_negociable,
                    //cpro_estado_banco,
                    //cpro_sucursal_pago,
                    //cpro_tipo_distrib,
                    //cpro_nro_e_cheq
			//',
            'whereArmado' => " WHERE ".$this->keyFieldAnita[0]." = '".$key1."' AND ".
                    $this->keyFieldAnita[1]." = '".$key2."' AND ".
                    $this->keyFieldAnita[2]." = '".$key3."' "
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));
		$usuario_id = Auth::user()->id;

        if (count($dataAnita) > 0) {
            $data = $dataAnita[0];

            Self::convierteDatosDeAnita($data, $estado, $fechaEmision, $fechaCheque, $cuentacaja_id, $empresa_id, $proveedor_id, 
                                        $estadoChequeBanco_id, $chequera_id);

            $arr_campos = [
                'origen' => 'E',
                'chequera_id' => $chequera_id,
                'caracter' => 'O',
                'estado' => $estado,
                'fechaemision' => $fechaEmision,
                'fechapago' => $fechaCheque,
                'cuentacaja_id' => $cuentacaja_id,
                'empresa_id' => $empresa_id,
                'caja_id' => null,
                'caja_movimiento_id' => null,
                'numerocheque' => $data->cpro_nro_cheque,
                'moneda_id' => $data->cpro_cod_mon,
                'monto' => $data->cpro_importe, 
                'cotizacion' => $data->cpro_cotizacion, 
                'proveedor_id' => $proveedor_id, 
                'cliente_id' => null,
                'tipodocumento_id' => null, 
                'numerodocumento' => null, 
                'entregado' => $data->cpro_entregado_a, 
                'anombrede' => $data->cpro_a_nombre_de, 
                'estadocheque_banco_id' => $estadoChequeBanco_id,
                'sucursalpago' => $data->cpro_sucursal_pago, 
                'tipodistribucion' => $data->cpro_tipo_distrib, 
                'banco_id' => null, 
                'codigopostalbanco' => null,
                'cuentalibradora' => null
                ];

            $this->model->create($arr_campos);
        }
    }

	public function guardarAnita($request) {
        $apiAnita = new ApiAnita();
        
        Self::convierteDatosParaAnita($request, $codigo, $fechaCheque, $fechaEmision, $proveedor, $modelo, 
                                        $caracter, $empresa, $negociable, $estadoBanco, $numeroEcheq,
                                        $estado);

        $data = array( 'tabla' => $this->tableAnita, 'acc' => 'insert',
			'sistema' => 'che_ban',
            'campos' => ' 
                    cpro_cuenta,
                    cpro_nro_cheque,
                    cpro_fecha_cheque,
                    cpro_fecha_emision,
                    cpro_importe,
                    cpro_proveedor,
                    cpro_entregado_a,
                    cpro_nro_op,
                    cpro_cod_mon,
                    cpro_cotizacion,
                    cpro_estado,
                    cpro_contrapartida,
                    cpro_fecha_anula,
                    cpro_fl_imprimio,
                    cpro_a_nombre_de,
                    cpro_modelo,
                    cpro_para_dep',
                    //,
                    //cpro_fecha_entrega,
                    //cpro_empresa,
                    //cpro_negociable,
                    //cpro_estado_banco,
                    //cpro_sucursal_pago,
                    //cpro_tipo_distrib,
                    //cpro_nro_e_cheq
				//',
            'valores' => " 
				'".str_pad($codigo, 8, "0", STR_PAD_LEFT)."', 
				'".$request['numerocheque']."',
				'".$fechaCheque."',
				'".$fechaEmision."',
				'".$request['monto']."',
				'".str_pad($proveedor, 6, "0", STR_PAD_LEFT)."',
				'".$request['entregado']."',
				'0',
				'".$request['moneda_id']."',
                '".$request['cotizacion']."',
                '".$estado."',
                ' ',
                '0',
				' ',
                '".$request['anombrede']."',
                '".$modelo."', 
                '".$caracter."'"
                //,
                //'0',
                //'".$empresa."',
                //'".$negociable."',
                //'".$estadoBanco."',
                //'".$request['sucursalpago']."',
                //'".$request['tipodistribucion']."',
                //.".$numeroEcheq."'"
        );
        $anita = $apiAnita->apiCall($data);

        return $anita;
	}

	public function actualizarAnita($request, $id) {
        $apiAnita = new ApiAnita();

        Self::convierteDatosParaAnita($request, $codigo, $fechaCheque, $fechaEmision, $proveedor, $modelo, 
                                        $caracter, $empresa, $negociable, $estadoBanco, $numeroEcheq,
                                        $estado);

		$data = array( 'acc' => 'update', 'tabla' => $this->tableAnita, 
				'sistema' => 'che_ban',
				'valores' => " 
                        cpro_fecha_cheque               = '".$fechaCheque."',
                        cpro_fecha_emision              = '".$fechaEmision."',
                        cpro_importe                    = '".$request['monto']."',
                        cpro_proveedor                  = '".str_pad($proveedor, 6, "0", STR_PAD_LEFT)."',
                        cpro_entregado_a                = '".$request['entregado']."',
                        cpro_cod_mon                    = '".$request['moneda_id']."',
                        cpro_cotizacion                 = '".$request['cotizacion']."',
                        cpro_estado                     = '".$estado."',
                        cpro_fecha_anula                = '".$fechaAnula."',,
                        cpro_a_nombre_de                = '".$request['anombrede']."',
                        cpro_modelo                     = '".$modelo."',
                        cpro_para_dep                   = '".$caracter."' "
                        //,
                        //cpro_empresa                    = '".$empresa."',
                        //cpro_negociable                 = '".$negociable."',
                        //cpro_estado_banco               = '".$estadoBanco."',
                        //cpro_sucursal_pago              = '".$request['sucursalpago']."',
                        //cpro_tipo_distrib               = '".$request['tipodistribucion']."',
                        //cpro_nro_e_cheq                 = '".$numeroEcheq."' "
				,
				'whereArmado' => " WHERE cpro_cuenta = '".str_pad($codigo, 8, "0", STR_PAD_LEFT)."' AND
                                    cpro_nro_cheque = '".$request['numerocheque']."'");
        $anita = $apiAnita->apiCall($data);

        return $anita;
	}

	public function eliminarAnita($cuenta, $numeroCheque) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita, 
				'sistema' => 'che_ban',
                'whereArmado' => " WHERE cpro_cuenta = '".str_pad($cuenta, 8, "0", STR_PAD_LEFT)."' AND
                                    cpro_nro_cheque = '".$numeroCheque."'");
        $anita = $apiAnita->apiCall($data);
        
        return $anita;
	}

    private function convierteDatosDeAnita($data, &$fechaEmision, &$fechaCheque, &$cuentacaja_id, &$empresa_id, 
                                            &$proveedor_id, &$estadoChequeBanco_id, &$chequera_id)
    {
        $fechaEmision = date('d-m-Y', strtotime($data->cpro_fecha_emision));
        $fechaCheque = date('d-m-Y', strtotime($data->cpro_fecha_cheque));

        $chequera = $this->chequeraRepository->select('id', 'codigo')->where('codigo' , $data->cpro_modelo)->first();
        if ($chequera)
            $chequera_id = $chequera->id;
        else
            $chequera_id = null;

        $cuentacaja = $this->cuentacajaRepository->select('id', 'codigo')->where('codigo' , ltrim($data->cpro_cuenta, '0'))->first();
        if ($cuentacaja)
            $cuentacaja_id = $cuentacaja->id;
        else
            $cuentacaja = null;

        $empresa = $this->empresaRepository->select('id', 'codigo')->where('codigo' , $data->cpro_empresa)->first();
        if ($empresa)
            $empresa_id = $empresa->id;
        else
            $empresa_id = null;

        $proveedor = $this->proveedorRepository->select('id', 'codigo')->where('codigo' , ltrim($data->cpro_proveedor, '0'))->first();
        if ($proveedor)
            $proveedor_id = $proveedor->id;
        else
            $proveedor_id = null;

        $estadocheque_banco = $this->estadocheque_bancoRepository->select('id', 'codigoexterno')
                                                                ->where('codigoexterno' , $data->cpro_estado_banco)
                                                                ->first();
        if ($estadocheque_banco)
            $estadoChequeBanco_id = $estadocheque_banco->id;
        else
            $estadoChequeBanco_id = null;
    }

    private function convierteDatosParaAnita($data, &$codigo, &$fechaCheque, &$fechaEmision, &$proveedor, &$modelo, 
                                        &$caracter, &$empresa, &$negociable, &$estadoBanco, &$numeroEcheq,
                                        &$estado)
    {
        $cuentacaja = $this->cuentacajaRepository->find($data['cuentacaja_id']);
        if ($cuentacaja)
            $codigo = $cuentacaja->codigo;
        else
            $codigo = null;

        $fechaEmision = date('Ymd', $data['fechaemision']);
        $fechaCheque = date('Ymd', $data['fechacheque']);

        $proveedor = $this->proveedorRepository->find($data['proveedor_id']);
        if ($proveedor)
            $proveedor = $proveedor->codigo;
        else
            $proveedor = null;

        $caracter = ($data['caracter'] == 'N' ? 'S' : 'N');

        $empresa = $this->empresaRepository->find($data['empresa_id']);
        if ($empresa)
            $empresa = $empresa->codigo;
        else
            $empresa = null;

        $chequera = $this->chequeraRepository->find($data['chequera_id']);
        if ($chequera)
        {
            $modelo = $chequera->codigo;
            $tipoChequera = $chequera->tipochequera;
        }
        else
        {
            $modelo = null;
            $tipoChequera = null;
        }
        if ($tipoChequera == 'E')
            $caracter = 'E';
        else
            $caracter = ($data['caracter'] == 'N' ? 'S' : 'N');

        switch($tipoChequera)
        {
        case 'F': // Fisica
            $negociable = 'N';
            break;
        case 'E': // Electronica
            $negociable = 'E';
            break;
        }

        $estadocheque_banco = $this->estadocheque_bancoRepository->find($data['estadocheque_banco_id']);

        if ($estadocheque_banco)
            $estadoBanco = $estadocheque_banco->codigoexterno;
        else
            $estadoBanco = null;

        $numeroEcheq = '';
        if ($negociable == 'E')
            $numeroEcheq = $data['numerocheque'];

        switch($data['estado'])
        {
        case 'DIFERIDO':
            $estado = ' ';
            break;
        case 'DEBITADO':
            $estado = '*';
            break;
        case 'CIERRE':
            $estado = 'C';
            break;
        case 'ANULADO':
            $estado = 'A';
            break;
        case 'RECHAZADO':
            $estado = 'R';
            break;
        case 'NO_PRESENTADO':
            $estado = 'N';
            break;
        }
    }
}
