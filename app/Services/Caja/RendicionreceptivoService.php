<?php
namespace App\Services\Caja;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Repositories\Configuracion\SeteosalidaRepositoryInterface;
use App\Repositories\Caja\RendicionreceptivoRepositoryInterface;
use App\Repositories\Caja\Rendicionreceptivo_Caja_MovimientoRepositoryInterface;
use App\Repositories\Caja\Rendicionreceptivo_VoucherRepositoryInterface;
use App\Repositories\Caja\Rendicionreceptivo_FormapagoRepositoryInterface;
use App\Repositories\Caja\Rendicionreceptivo_ComisionRepositoryInterface;
use App\Repositories\Caja\Rendicionreceptivo_AdelantoRepositoryInterface;
use App\Repositories\Caja\Caja_MovimientoRepositoryInterface;
use App\Repositories\Caja\CuentacajaRepositoryInterface;
use App\Repositories\Caja\VoucherRepositoryInterface;
use App\Repositories\Caja\Voucher_GuiaRepositoryInterface;
use App\Services\Caja\IngresoEgresoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App;
use Auth;
use DB;
use Exception;

class RendicionreceptivoService 
{
	private $rendicionreceptivoRepository;
    private $rendicionreceptivo_caja_movimientoRepository;
    private $rendicionreceptivo_voucherRepository;
    private $rendicionreceptivo_formapagoRepository;
    private $rendicionreceptivo_comisionRepository;
    private $rendicionreceptivo_adelantoRepository;
    private $caja_movimientoRepository;
    private $cuentacajaRepository;
    private $voucherRepository;
    private $voucher_guiaRepository;
    private $ingresoegresoService;

    public function __construct(RendicionreceptivoRepositoryInterface $rendicionreceptivorepository,
                                Rendicionreceptivo_Caja_MovimientoRepositoryInterface $rendicionreceptivo_caja_movimientorepository,
                                Rendicionreceptivo_VoucherRepositoryInterface $rendicionreceptivo_voucherrepository,
                                Rendicionreceptivo_FormapagoRepositoryInterface $rendicionreceptivo_formapagorepository,
                                Rendicionreceptivo_ComisionRepositoryInterface $rendicionreceptivo_comisionrepository,
                                Rendicionreceptivo_AdelantoRepositoryInterface $rendicionreceptivo_adelantorepository,
                                Caja_MovimientoRepositoryInterface $caja_movimientorepository,
                                CuentacajaRepositoryInterface $cuentacajarepository,
                                VoucherRepositoryInterface $voucherrepository,
                                Voucher_GuiaRepositoryInterface $voucher_guiarepository,
                                IngresoEgresoService $ingresoegresoservice
								)
    {
		$this->rendicionreceptivoRepository = $rendicionreceptivorepository;
        $this->rendicionreceptivo_caja_movimientoRepository = $rendicionreceptivo_caja_movimientorepository;
        $this->caja_movimientoRepository = $caja_movimientorepository;
        $this->voucherRepository = $voucherrepository;
        $this->voucher_guiaRepository = $voucher_guiarepository;
        $this->cuentacajaRepository = $cuentacajarepository;
        $this->rendicionreceptivo_voucherRepository = $rendicionreceptivo_voucherrepository;
        $this->rendicionreceptivo_formapagoRepository = $rendicionreceptivo_formapagorepository;
        $this->rendicionreceptivo_adelantoRepository = $rendicionreceptivo_adelantorepository;
        $this->rendicionreceptivo_comisionRepository = $rendicionreceptivo_comisionrepository;
        $this->ingresoegresoService = $ingresoegresoservice;
    }

	public function guardaRendicionreceptivo($request)
	{
		session(['empresa_id' => 1]);
		$data = $request->all();

		DB::beginTransaction();
        try
        {
            $rendicionreceptivo = $this->rendicionreceptivoRepository->create($request->all());

            if ($rendicionreceptivo == 'Error')
                throw new Exception('Error en grabación transacción');

            // Guarda tablas asociadas
            if ($rendicionreceptivo)
            {
                $rendicionreceptivo_caja_movimiento = $this->rendicionreceptivo_caja_movimientoRepository->create($data, $rendicionreceptivo->id);
                $rendicionreceptivo_voucher = $this->rendicionreceptivo_voucherRepository->create($data, $rendicionreceptivo->id);
                $rendicionreceptivo_formapago = $this->rendicionreceptivo_formapagoRepository->create($data, $rendicionreceptivo->id);
                $rendicionreceptivo_comision = $this->rendicionreceptivo_comisionRepository->create($data, $rendicionreceptivo->id);
                $rendicionreceptivo_adelanto = $this->rendicionreceptivo_adelantoRepository->create($data, $rendicionreceptivo->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            // Borra el asiento creado

            return ['errores' => $e->getMessage()];
        }
        return ['mensaje' => 'ok'];
	}

    public function actualizaRendicionreceptivo($request, $id)
    {
        session(['empresa_id' => $request->empresa_id]);
		$data = $request->all();

        DB::beginTransaction();
        try
        {
            // Graba movimiento de caja
            $rendicionreceptivo = $this->rendicionreceptivoRepository->update($data, $id);

			if ($rendicionreceptivo === 'Error')
                throw new Exception('Error en grabación transacción.');

            // Graba movimientos de cuentas de caja
            $this->rendicionreceptivo_caja_movimientoRepository->update($data, $id);

			// Graba movimientos de voucher
            $this->rendicionreceptivo_voucherRepository->update($data, $id);

			// Graba formapagos  
            $this->rendicionreceptivo_formapagoRepository->update($data, $id);

			// Graba comisiones  
            $this->rendicionreceptivo_comisionRepository->update($data, $id);
            
			// Graba adelantos  
            $this->rendicionreceptivo_adelantoRepository->update($data, $id);

            // Graba ingresos y egresos
            Self::armaIngresoEgreso($request);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return ['errores' => $e->getMessage()];
        }
        return ['mensaje' => 'ok'];
    }

	public function borraRendicionreceptivo($id)
	{
	}

    public function armaIngresoEgreso($request)
    {
        // Graba asiento contable
        if ($request->cuentacaja_ids)
        {
            for ($i = 0; $i < count($request->cuentacaja_ids); $i++)
            {
                // Lee la cuenta de caja
                $cuentacaja = $this->cuentacajaRepository->find($request->cuentacaja_ids[$i]);

                if ($cuentacaja)
                {
                    $request->request->add(['cuentacontable_ids' => $cuentacaja->cuentacontable_id]);
                    $request->request->add(['monedaasiento_ids' => $request->moneda_ids[$i]]);
                    $request->request->add(['centrocostoasiento_ids' => 0]);
                    $request->request->add(['haberasientos' => $request->montos[$i]]);
                    $request->request->add(['debeasientos' => 0]);
                    $request->request->add(['cotizacionasientos' =>$request->cotizaciones[$i]]);
                    $request->request->add(['observacionasientos' => '']);
                }
            }
        }
        // Graba gastos a compensar
        dd($request);
    }

    public function leeGastoAnterior($ordenservicio_id)
    {
        $gastoAnterior = [];
        $adelanto = [];

        $caja_movimiento = $this->caja_movimientoRepository->leeGastoAnterior($ordenservicio_id);

        foreach($caja_movimiento as $movimiento)
        {
            if ($movimiento->abreviatura != "ADE")
                $gastoAnterior[] = [    
							'id' => $movimiento->id,
							'nombregasto' => $movimiento->nombreconceptogasto,
							'codigocuentacaja' => $movimiento->codigocuentacaja,
							'nombrecuentacaja' => $movimiento->nombrecuentacaja,
							'abreviaturamoneda' => $movimiento->abreviaturamoneda,
							'moneda_id' => $movimiento->moneda_id,
							'cotizacion' => $movimiento->cotizacion,
							'monto' => abs($movimiento->monto)
                ];
            else
                $adelanto[] = [    
							'id' => $movimiento->id,
							'nombregasto' => $movimiento->nombreconceptogasto,
							'codigocuentacaja' => $movimiento->codigocuentacaja,
							'nombrecuentacaja' => $movimiento->nombrecuentacaja,
							'abreviaturamoneda' => $movimiento->abreviaturamoneda,
							'moneda_id' => $movimiento->moneda_id,
							'cotizacion' => $movimiento->cotizacion,
							'monto' => abs($movimiento->monto)
                ];
        }
        return ['mensaje' => 'ok', 'gastoanterior' => $gastoAnterior, 'adelanto' => $adelanto];
    }

    public function leeVoucher($guia_id, $ordenservicio_id)
    {
        $arrayVoucher = [];
        $arrayComision = [];

        $vouchers = $this->voucherRepository->leeVoucherPorGuiaOrdenservicio($guia_id, $ordenservicio_id);

        $comisiones = $this->voucher_guiaRepository->leeComisionPorGuiaOrdenservicio($guia_id, $ordenservicio_id);

        foreach($vouchers as $voucher)
        {
            $arrayVoucher[] = [    
							'id' => $voucher->id,
							'fecha' => $voucher->fecha,
                            'cuentacaja_id' => $voucher->cuentacaja_id,
							'codigocuentacaja' => $voucher->codigocuentacaja,
							'nombrecuentacaja' => $voucher->nombrecuentacaja,
							'abreviaturamoneda' => $voucher->abreviaturamoneda,
							'moneda_id' => $voucher->moneda_id,
							'cotizacion' => $voucher->cotizacion,
							'monto' => abs($voucher->monto)
            ];
        }

        foreach($comisiones as $comision)
        {
            $arrayComision[] = [
							'id' => $comision->id,
							'fecha' => $comision->fecha,
                            'cuentacaja_id' => $comision->cuentacaja_id,
							'codigocuentacaja' => $comision->codigocuentacaja,
							'nombrecuentacaja' => $comision->nombrecuentacaja,
							'abreviaturamoneda' => $comision->abreviaturamoneda,
							'moneda_id' => $comision->moneda_id,
							'cotizacion' => $comision->cotizacion,
							'monto' => abs($comision->monto)
            ];
        }
        return ['mensaje' => 'ok', 'voucher' => $arrayVoucher, 'comision' => $arrayComision];
    }
}