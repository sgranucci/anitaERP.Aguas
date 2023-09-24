<?php

namespace App\Jobs;

use App\Services\Graficos\IndicadoresService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use DB;

class GeneraOrdenes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //public $tries = 5;
    //public $backoff = 1;
    private $especie;
    private $calculoBase;
    private $mmCorta;
    private $mmLarga;
    private $compresion;
    private $largoVMA;
    private $largoCCI;
    private $largoXTL;
    private $umbralXTL;
    private $swingSize;
    private $filtroSetup;
    private $factorCompresion;

    public $timeout = 36000;

    public $failOnTimeout = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($especie, $calculobase, 
                                $mmcorta, $mmlarga, $compresion, $largovma, $largocci, $largoxtl,
                                $umbralxtl, $swingSize, $filtroSetup)
    {
        $this->especie = $especie;
        $this->calculoBase = $calculobase;
        $this->mmCorta = $mmcorta;
        $this->mmLarga = $mmlarga;
        $this->compresion = $compresion;
        $this->largoVMA = $largovma;
        $this->largoCCI = $largocci;
        $this->largoXTL = $largoxtl;
        $this->umbralXTL = $umbralxtl;
        $this->swingSize = $swingSize;
        $this->filtroSetup = $filtroSetup;
        $this->factorCompresion = 5;
        switch($compresion)
        {
        case 1:
            $this->factorCompresion = 1;
            break;
        case 2:
            $this->factorCompresion = 5;
            break;
        case 3:
            $this->factorCompresion = 15;
            break;
        case 4:
            $this->factorCompresion = 60;
            break;
        case 5:
            $this->factorCompresion = 3600;
            break;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        $indicadoresService = new IndicadoresService;

        $currId = 0;
        $q = 0;

        // Procesa compresion
        $indicadoresService->acumOpen = 0;
        $indicadoresService->acumClose = $indicadoresService->acumLow = $indicadoresService->acumHigh = 0;
        $indicadoresService->acumVolume = 0;
        $indicadoresService->acumCantLectura = 0;
        $indicadoresService->acumFlEmpezoRango = false;
        $indicadoresService->acumItem = 0;
        $indicadoresService->acumFecha = "01-01-2001";
        $indicadoresService->acumFechaInicioRango = "01-01-2001";
        $indicadoresService->acumFechaStr = "01-01-2001";
        $indicadoresService->acumFechaLectura = "01-01-2001";
        $indicadoresService->cantLectura = 0;

        $dataPrueba = DB::connection('trade')->table('trade.lecturas')
            ->select('id',
                    'fechalectura',
                    'chartTime as fecha',
                    'openPrice as open',
                    'highPrice as high',
                    'lowPrice as low',
                    'closePrice as close',
                    'volume')
            ->where('especie', $this->especie)
            ->where('fecha','>','2023-09-04')
            ->where('fecha','<','2023-09-09')
            ->get();
        do
        {
            $cantidadLeidos = 0;
            foreach($dataPrueba as $data)
            {
                $cantidadLeidos++;
            //$data = DB::connection('trade')->table('trade.lecturas')
            //    ->select('id',
            //        'fechalectura',
            //        'chartTime as fecha',
            //        'openPrice as open',
            //        'highPrice as high',
            //        'lowPrice as low',
            //        'closePrice as close',
            //        'volume')
            //->where('especie', $this->especie)
            //->orderBy('id', 'DESC')
            //->first();
                if ($data->id > $currId)
                    break;
            }
            if ($data->id != $currId)
            {
                $currId = $data->id;

                //Log::info($data->fechalectura.' '.$data->open." ".$data->high);

                $indicadoresService->generaDatosOrdenes($data, $this->especie, $this->calculoBase, 
                        $this->mmCorta, $this->mmLarga, $this->compresion, $this->largoVMA, $this->largoCCI, 
                        $this->largoXTL, $this->umbralXTL, $this->swingSize, $this->filtroSetup,
                        $this->factorCompresion);
            }
        } while ($currId == $data->id && $cantidadLeidos < count($dataPrueba));

        // Graba los que faltan
        if ($indicadoresService->acumItem <= 500)
            $indicadoresService->grabaTablaIndicadores();

        Log::info("Finalizo correctamente");
    }

    public function retryUntil()
    {
        return now()->addSeconds(10);
    }
}
