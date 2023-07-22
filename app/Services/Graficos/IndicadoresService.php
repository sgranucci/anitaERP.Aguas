<?php

namespace App\Services\Graficos;

use Carbon\Carbon;
use DB;

class IndicadoresService
{
	private $desdeFecha, $hastaFecha;
    private $desdeHora, $hastaHora;

    public $especie;
    public $compresion, $compresiontxt, $factorCompresion;
    public $calculoBase, $mmCorta, $mmLarga, $calculoBase_enum;
    public $largoVMA, $largoCCI, $largoXTL, $umbralXTL;
    public $swingSize;
    public $filtroSetup;
    public $ticker, $valorTicker;
    public $totalContratos;
    public $cantidadActivaContratos;
    public $item;

    // Variables de job para generar ordenes
    public $acumOpen = 0;
    public $acumClose = 0;
    public $acumLow = 0;
    public $acumHigh = 0;
    public $acumTotVolume = 0;
    public $cantLectura = 0;
    public $acumItem = 0;
    public $acumFechaLectura;
    public $acumCantLectura;
    public $acumFlEmpezoRango;
    public $acumMinutoLectura;
    public $acumFecha;
    public $acumFechaInicioRango;
    public $acumHoraInicio;
    
    private $flBatch;
    private $k1, $k2;
    private $dataAnterior = [];
    private $fechaUltimaLectura;
    private $datas = [];
    public $operaciones = [];
	protected $dates = ['fecha'];
    private $offsetMinimoE;
    private $offsetMaximoD;
    private $offsetMinimoC;
    private $offsetMaximoB;
    private $offsetMinimoA;
    private $offsetMinimoD;
    private $offsetMaximoC;
    private $offsetMinimoB;
    private $offsetMaximoA;
    private $offsetAbcd;
    private $offset3Drives;
    private $offsetShark;
    private $offsetW4;
    private $offsetSp;
	private $tgt = [];
    private $pivotes = [];

	public function calculaIndicadores($desdefecha, $hastafecha, $desdehora, $hastahora, $especie, $calculobase, 
                                        $mmcorta, $mmlarga, $compresion, $largovma, $largocci, $largoxtl,
                                        $umbralxtl, $calculobase_enum, $swingSize, $filtroSetup, 
                                        $totalContratos)
	{
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '2400');

        $this->desdeFecha = $desdefecha;
		$this->hastaFecha = $hastafecha;
		$this->desdeHora = $desdehora;
		$this->hastaHora = $hastahora;
        $this->especie = $especie;
		$this->compresion = $compresion;
        $this->calculoBase = $calculobase;
        $this->calculoBase_enum = $calculobase_enum;
        $this->mmCorta = $mmcorta;
        $this->mmLarga = $mmlarga;
        $this->largoVMA = $largovma;
        $this->largoCCI = $largocci;
        $this->largoXTL = $largoxtl;
        $this->umbralXTL = $umbralxtl;
        $this->swingSize = $swingSize;
        $this->filtroSetup = $filtroSetup;
        $this->totalContratos = $totalContratos;
        $this->ticker = 0.25;
        $this->valorTicker = 12.5;

        switch($compresion)
        {
        case 1:
            $this->compresiontxt = "1 minuto";
            $this->factorCompresion = 1;
            break;
        case 2:
            $this->compresiontxt = "5 minutos";
            $this->factorCompresion = 5;
            break;
        case 3:
            $this->compresiontxt = "15 minutos";
            $this->factorCompresion = 15;
            break;
        case 4:
            $this->compresiontxt = "1 hora";
            $this->factorCompresion = 60;
            break;
        case 5:
            $this->compresiontxt = "1 día";
            $this->factorCompresion = 3600;
            break;
        }

        if ($this->factorCompresion == 3600)
        {
            $fechaAnterior = date("d-m-Y",strtotime($this->desdeFecha."- 1 days")); 
            $desde_fecha = strtotime($fechaAnterior.' '.'19:00')*1000;
        }
        else
            $desde_fecha = strtotime($this->desdeFecha.' '.$this->desdeHora)*1000;

        $hasta_fecha = strtotime($this->hastaFecha.' '.$this->hastaHora)*1000;
        $calculoBaseTxt = $this->calculoBase_enum[$this->calculoBase];
        $this->k2 = 2 / ($this->mmCorta + $this->mmLarga);
        $this->k1 = 1 - $this->k2;
        $this->flBatch = false;
                
		$data = DB::connection('trade')->table('trade.lecturas')
				->select('fechaChar as fechastr',
                         'chartTime as fecha',
						 'openPrice as open',
						 'highPrice as high',
						 'lowPrice as low',
						 'closePrice as close',
						 'volume')
                ->where('especie', $this->especie)
				->whereBetween('chartTime', [$desde_fecha, $hasta_fecha])
                ->get();

        // Procesa compresion
        $open = $close = $low = $high = $totVolume = 0;
        $closeAnt = 0;
        $cantLectura = 0;
        $this->datas = [];
        $this->operaciones = [];
        $item = 0;
        if ($this->factorCompresion == 1)
            $flEmpezoRango = true;    
        else
            $flEmpezoRango = false;
        foreach($data as $lectura)
        {
            // Saltea fechas repetidas
            if (isset($fechaLectura))
            {
                if (date('Y-m-d H:i', ceil($lectura->fecha/1000)) == $fechaLectura)
                    continue;
            }

            $fechaLectura = date('Y-m-d H:i', ceil($lectura->fecha/1000));
            $minutoLect = date('i', ceil($lectura->fecha/1000));

            // Verifica arrancar en divisor del factor del compresion
            if ($this->factorCompresion > 1 && !$flEmpezoRango)
            {
                if ($minutoLect % $this->factorCompresion == 0)
                    $flEmpezoRango = true;
            }
            if ($flEmpezoRango)
            {
                // Corte Si es por dia
                $flCorte = false;
                if ($this->factorCompresion == 3600)
                {
                    $horaLect = date('i', ceil($lectura->fecha/1000));
                    // Corta el dia a las 17:59
                    if ($horaLect >= '17:59' && $horaLect < '19:00')
                        $flCorte = true;
                }
                else // Corte si es por minutos
                {
                    if (!isset($fechaInicioRango))
                        $fechaInicioRango = date('Y-m-d H:i', ceil($lectura->fecha/1000));
                        
                    $difMinutos = \Carbon\Carbon::parse($fechaInicioRango)->diffInMinutes($fechaLectura);
                    if ($difMinutos >= $this->factorCompresion ||
                        ($minutoLect % $this->factorCompresion == 0 && isset($fecha)))
                        $flCorte = true;
                }
                if ($flCorte)
                {
                    switch($this->calculoBase)
                    {
                        case 1: // HL2
                            $base = ($high + $low) / 2;
                            break;
                        case 2: // HLC3
                            $base = ($high + $low + $close) / 3;
                            break;
                        case 3: // OHLC4
                            $base = ($open + $high + $low + $close) / 4;
                            break;
                    }
                    // Calcula EWO                    
                    $item++;
                    $ewo = $bandaSup = $bandaInf = 0;
					$smac = $smal = 0;
                    $w4Up1 = $w4Up2 = $w4Dw1 = $w4Dw2 = 0;
                    $this->calculaEWO($item, $base, $smac, $smal, $ewo, $bandaSup, $bandaInf,
                                        $w4Up1, $w4Up2, $w4Dw1, $w4Dw2);
                    
                    // Calcula pivot de fibonacci
                    $this->calculaFibonacci($fechaInicioRango, $rfLim, $rfeExt, $rfeInt, $rfiExt, $rfiInt, $pp1,
                                            $poc, $pp2, $sfiInt, $sfiExt, $sfeInt, $sfeExt, $sfLim, $base);

                    $tmp1 = $tmp2 = $d1 = $d2 = $condicional = $d3 = $k = $VMA = $precioTipico = 0;
                    $SMACCI = $auxCCI = $blanco1 = $blanco2 = $CCI = $SMAXTL = $auxXTL = $CCIXTL = 0;
                    $estado = $rango = $TQRVerde = $stopTQRVerde = $tgtTQRVerde = $TQRRojo = 0;
                    $stopTQRRojo = $tgtTQRRojo = 0;
                    // Calcula VMA
                    $this->CalculaVMA($item, $closeAnt, $close, $tmp1, $tmp2, $d1, $d2, $condicional, $d3, $k, $VMA);
                    $closeAnt = $close;

                    // Calcula CCI
                    $this->CalculaCCI($item, $high, $low, $close, $precioTipico, $SMACCI, $auxCCI,
                        $blanco1, $blanco2, $CCI);

                    // Calcula XTL
                    $this->CalculaXTL($item, $high, $low, $precioTipico, $SMAXTL, $auxXTL, $CCIXTL, $estado, $rango, 
                                    $TQRVerde, $stopTQRVerde, $tgtTQRVerde,
                                    $TQRRojo, $stopTQRRojo, $tgtTQRRojo);

                    // Arma tabla 
                    $this->armaTabla($fechaStr, $fecha, $horaInicio, $open, $close, $low, $high, $totVolume, $ewo,
                                    $bandaSup, $bandaInf, $w4Up1, $w4Up2, $w4Dw1, $w4Dw2,
                                    $rfLim, $rfeExt, $rfeInt, $rfiExt, $rfiInt, $pp1, $poc, $pp2,
                                    $sfiInt, $sfiExt, $sfeInt, $sfeExt, $sfLim, $base, $smac, $smal, $tmp1, $tmp2, $d1, $d2,
                                    $condicional, $d3, $k, $VMA, $precioTipico, $SMACCI, $auxCCI, $blanco1, $blanco2,
                                    $CCI, $SMAXTL, $auxXTL, $CCIXTL, $estado, $rango, $TQRVerde, $stopTQRVerde, $tgtTQRVerde,
                                    $TQRRojo, $stopTQRRojo, $tgtTQRRojo);
                                    
                    $cantLectura = 0;
                    $low = $high = $totVolume = $open = $close = 0;
                }

                $fecha = $lectura->fecha;
                $totVolume += $lectura->volume;
                $cantLectura++;

                // Si es primer lectura del rango inicia variables
                if ($cantLectura == 1)
                {
                    $fechaInicioRango = $fechaLectura;
                    $fechaStr = $lectura->fechastr;
                    $horaInicio = date('H:i:s', ceil($lectura->fecha/1000));
                    $open = $lectura->open;
                    $low = $lectura->low;
                    $high = $lectura->high;
                }
                else
                {
                    if ($lectura->low < $low)
                        $low = $lectura->low;
                    if ($lectura->high > $high)
                        $high = $lectura->high;
                }  
                $close = $lectura->close;
            }
        }
        // Por si quedo ultimo rango sin cerrar
        if ($cantLectura > 1)
        {
            $this->armaTabla($fechaStr, $fecha, $horaInicio, $open, $close, $low, $high, $totVolume, $ewo,
                                $bandaSup, $bandaInf, $w4Up1, $w4Up2, $w4Dw1, $w4Dw2,
                                $rfLim, $rfeExt, $rfeInt, $rfiExt, $rfiInt, $pp1, $poc, $pp2,
                                $sfiInt, $sfiExt, $sfeInt, $sfeExt, $sfLim, $base, $smac, $smal, $tmp1, $tmp2, $d1, $d2,
                                $condicional, $d3, $k, $VMA, $precioTipico, $SMACCI, $auxCCI, $blanco1, $blanco2,
                                $CCI, $SMAXTL, $auxXTL, $CCIXTL, $estado, $rango, $TQRVerde, $stopTQRVerde, $tgtTQRVerde,
                                $TQRRojo, $stopTQRRojo, $tgtTQRRojo);
                                
        }

        // Calcula pivots
        $this->calculaPivot();

        // Calcula volumen por swing y Tgt hit
        $this->calculaSwingTgt();
       
        return ['indicadores' => $this->datas, 'operaciones' => $this->operaciones];
    }

    private function calculaEWO($item, $base, &$smac, &$smal, &$ewo, &$bandaSup, &$bandaInf, &$w4Up1, &$w4Up2, &$w4Dw1, &$w4Dw2)
    {
        if ($item >= $this->mmLarga)
        {
            $smac = $this->promedio($base, $item, 'base', $this->mmCorta);
            $smal = $this->promedio($base, $item, 'base', $this->mmLarga);
            $ewo = $smac - $smal;

            if ($ewo > 0)
                $bandaSup = $this->datas[$item-2]['bandaSup'] + $this->k2 * ($this->k1 * $ewo - $this->datas[$item-2]['bandaSup']);
            else
                $bandaSup = $this->datas[$item-2]['bandaSup'];
            if ($ewo < 0)
                $bandaInf = $this->datas[$item-2]['bandaInf'] + $this->k2 * ($this->k1 * $ewo - $this->datas[$item-2]['bandaInf']);
            else
                $bandaInf = $this->datas[$item-2]['bandaInf'];

            $w4Up1 = $w4Up2 = $w4Dw1 = $w4Dw2 = 0;
            if ($item > 2)
            {
                if ($ewo > max($bandaSup, $this->datas[$item-2]['w4Dw2']))
                {
                    $w4Up1 = max($this->datas[$item-2]['ewo']*(1-0.9), $this->datas[$item-2]['w4Up1']);
                    $w4Up2 = min($this->datas[$item-2]['ewo']*(1-1.3), $this->datas[$item-2]['w4Up2']);
                }
                elseif ($ewo < min($bandaInf, $this->datas[$item-2]['w4Up2']))
                {
                    $w4Dw1 = min($this->datas[$item-2]['ewo']*(1-0.9), $this->datas[$item-2]['w4Dw1']);
                    $w4Dw2 = max($this->datas[$item-2]['ewo']*(1-1.3), $this->datas[$item-2]['w4Dw2']);
                }
                else
                {
                    $w4Up1 = $this->datas[$item-2]['w4Up1'];
                    $w4Up2 = $this->datas[$item-2]['w4Up2'];
                    $w4Dw1 = $this->datas[$item-2]['w4Dw1'];
                    $w4Dw2 = $this->datas[$item-2]['w4Dw2'];
                }
            }
        }
    }

    private function calculaFibonacci($fechaLectura, &$rfLim, &$rfeExt, &$rfeInt, &$rfiExt, &$rfiInt, &$pp1,
                                    &$poc, &$pp2, &$sfiInt, &$sfiExt, &$sfeInt, &$sfeExt, &$sfLim, &$base)
    {
        $oant = $hant = $lant = $cant = 0;

        $this->calculaDatosDiaAnterior($fechaLectura, $oant, $cant, $lant, $hant);

        $poc = ($hant + $lant + $cant) / 3;
        $rango = $hant - $lant;
        $tolerancia = $rango * 0.05;
        $x1 = $rango * 0.5;
        $x2 = $rango * 0.618;
        $x3 = $rango;
        $x4 = $rango * 1.382;
        $x5 = $rango * 2.618;
        $pp1 = $poc + $tolerancia;
        $pp2 = $poc - $tolerancia;
        $sfiInt = $poc - $x1 + $tolerancia;
        $sfiExt = $poc - $x2 - $tolerancia;
        $sfeInt = $poc - $x3 + $tolerancia;
        $sfeExt = $poc - $x4 - $tolerancia;
        $sfLim = $poc - $x5;
        $rfiInt = $poc + $x1 - $tolerancia;
        $rfiExt = $poc + $x2 + $tolerancia;
        $rfeInt = $poc + $x3 - $tolerancia;
        $rfeExt = $poc + $x4 + $tolerancia;
        $rfLim = $poc + $x5;
    }

    private function CalculaVMA($item, $closeAnt, $close, &$tmp1, &$tmp2, &$d1, &$d2, 
                                &$condicional, &$d3, &$k, &$VMA)
    {
        if ($close > $closeAnt && $item > 1)
            $tmp1 = $close - $closeAnt;
        else   
            $tmp1 = 0;

        if ($close < $closeAnt)
            $tmp2 = $closeAnt - $close;
        else    
            $tmp2;

        $d1 = $d2 = 0;
        if ($item >= $this->largoVMA)
        {
            $d1 = $this->acumulado($tmp1, $item, 'tmp1', $this->largoVMA);
            $d2 = $this->acumulado($tmp2, $item, 'tmp2', $this->largoVMA);
        }
        $condicional = $d1 + $d2;
        
        if ($condicional != 0)
            $d3 = ($d1 - $d2) / ($d1 + $d2) * 100;
        else   
            $d3 = 0;

        $k = (2 / ($this->largoVMA + 1)) * ABS($d3) / 100;  
        $anteriorVMA = 0;
        if ($item > 2) 
            $anteriorVMA = $this->datas[$item-2]['VMA'];
        $VMA = ($close * $k) + ($anteriorVMA * (1 - $k));
    }

    private function CalculaCCI($item, $high, $low, $close, &$precioTipico, &$SMACCI, &$auxCCI,
                        &$blanco1, &$blanco2, &$CCI)
    {
        $precioTipico = ($high + $low + $close) / 3;
        
        $SMACCI = $CCI = 0;
        if ($item >= $this->largoCCI)
        {
            $SMACCI = $this->promedio($precioTipico, $item, 'precioTipico', $this->largoCCI);
            $this->lindev($item, $precioTipico, $this->largoCCI, $auxCCI);
            $CCI = ($precioTipico - $SMACCI) / (0.015 * $auxCCI);
        }
    }

    private function CalculaXTL($item, $high, $low, $precioTipico, &$SMAXTL, &$auxXTL, &$CCIXTL, 
                                &$estado, &$rango, 
                                &$TQRVerde, &$stopTQRVerde, &$tgtTQRVerde,
                                &$TQRRojo, &$stopTQRRojo, &$tgtTQRRojo)
    {
        $SMAXTL = $auxXTL = $CCIXTL = 0;
        if ($item >= $this->largoXTL)
        {
            $SMAXTL = $this->promedio($precioTipico, $item, 'precioTipico', $this->largoXTL);
            $this->lindev($item, $precioTipico, $this->largoXTL, $auxXTL);
        }
        if ($auxXTL != 0)
            $CCIXTL = ($precioTipico - $SMAXTL) / (0.015 * $auxXTL);
        
        $estado = "";
        if ($CCIXTL < $this->umbralXTL && $auxXTL > -$this->umbralXTL)
            $estado = "neutral";
        if ($CCIXTL > $this->umbralXTL)
            $estado = "verde";
        if ($CCIXTL < -$this->umbralXTL)
            $estado = "rojo";

        $rango = 0;
        $TQRVerde = 0;
        $stopTQRVerde = 0;
        $tgtTQRVerde = 0;
        $TQRRojo = 0;
        $stopTQRRojo = 0;
        $tgtTQRRojo = 0;
        
        if ($item > 2)
        {
            if ($estado != $this->datas[$item-2]['estado'] && $estado != 'neutral')
                $rango = ($high - $low) / 2;
            
            if ($this->datas[$item-2]['estado'] != 'verde' && $estado == 'verde')
                $TQRVerde = $high + $rango;
            elseif ($this->datas[$item-2]['estado'] == 'verde' && $estado == 'verde')
                $TQRVerde = $this->datas[$item-2]['TQRVerde'];

            if ($this->datas[$item-2]['estado'] != 'verde' && $estado == 'verde')
                $stopTQRVerde = $low - $rango;
            elseif ($this->datas[$item-2]['estado'] == 'verde' && $estado == 'verde')
                $stopTQRVerde = $this->datas[$item-2]['stopTQRVerde'];

            if ($this->datas[$item-2]['estado'] != 'verde' && $estado == 'verde')
                $tgtTQRVerde = $high + ($rango * 4);
            elseif ($this->datas[$item-2]['estado'] == 'verde' && $estado == 'verde')
                $tgtTQRVerde = $this->datas[$item-2]['tgtTQRVerde'];

            if ($this->datas[$item-2]['estado'] != 'rojo' && $estado == 'rojo')
                $TQRRojo = $low - $rango;
            elseif ($this->datas[$item-2]['estado'] == 'rojo' && $estado == 'rojo')
                $TQRRojo = $this->datas[$item-2]['TQRRojo'];

            if ($this->datas[$item-2]['estado'] != 'rojo' && $estado == 'rojo')
                $stopTQRRojo = $high + $rango;
            elseif ($this->datas[$item-2]['estado'] == 'rojo' && $estado == 'rojo')
                $stopTQRRojo = $this->datas[$item-2]['stopTQRRojo'];
            
            if ($this->datas[$item-2]['estado'] != 'rojo' && $estado == 'rojo')
                $tgtTQRRojo = $low - ($rango * 4);
            elseif ($this->datas[$item-2]['estado'] == 'rojo' && $estado == 'rojo')
                $tgtTQRRojo = $this->datas[$item-2]['tgtTQRRojo'];
        }
    }
    
    private function calculaPivot()
    {
        $trend = 0;
        $maxRango = 0;
        $minRango = 99999999999;
        $nroMin = $nroMax = 0;
        $bnMin = $bnMax = 0;
        $bnMinOriginal = 0;
        $pivot3 = $pivot2 = $pivot1 = 0;
        $cswing = 1;
        $flMax = false; $barrasDesdeMinimo = 0;
        $n = $this->swingSize;
        $bnMinActual = $bnMaxActual = 0;
        $this->pivotes = [];
        for ($i = $n - 1; $i < count($this->datas); $i++)
        {
            $maxRango = 0;
            $minRango = 999999999999;
            for ($j = $i - $n + 1; $j <= $i; $j++)
            {
                if ($this->datas[$j]['high'] > $maxRango)
                    $maxRango = $this->datas[$j]['high'];
                if ($this->datas[$j]['low'] < $minRango)
                    $minRango = $this->datas[$j]['low'];
            }

            $low = $this->datas[$i]['low'];
            $high = $this->datas[$i]['high'];
            $volumen = $this->datas[$i]['volume'];
            $minimoActual = $minRango;
            $maximoActual = $maxRango;

            // Calcula maximos y minimos candidatos
            if ($low <= $minimoActual && $i > $n)
            {
                // Fija nuevo minimo
                $minimoActual = $low;

                // Busca ultimo minimo en swingsize
                for ($j = $i - $n + 1; $j <= $i; $j++)
                {
                    if ($minimoActual <= $this->datas[$j]['low'])
                    {
                        $this->datas[$i]['provMin'] = $minimoActual;
                        $bnMinActual = $i;

                        // Pone el ultimo maximo candidato como definitivo
                        $this->datas[$bnMaxActual]['max'] = $this->datas[$bnMaxActual]['provMax'];
                        $this->datas[$bnMaxActual]['trendBars'] = 1;

                        // Calcula trendbars
                        for ($b = $bnMaxActual+1; $b <= $i; $b++)
                            $this->datas[$b]['trendBars'] = $this->datas[$b-1]['trendBars'] + 1;

                        // Actualiza swingbars
                        $this->calculaSwingSize($i, $bnMinActual, $bnMaxActual, 'MAXIMO');

                        // Calcula pivotes
                        $this->calculaPivotes($bnMaxActual, 0, $this->datas[$bnMaxActual]['max'], 
                            $bnMinActual, $bnMaxActual, 'MAXIMO');
                        break;
                    }
                }
            }
            else
            {
                if ($high >= $maximoActual && $i > $n)
                {
                    $maximoActual = $high;

                    // Busca ultimo maximo en swingsize
                    for ($j = $i - $n + 1; $j <= $i; $j++)
                    {
                        if ($maximoActual >= $this->datas[$j]['high'])
                        {
                            $this->datas[$i]['provMax'] = $maximoActual;
                            $bnMaxActual = $i;

                            // Pone el ultimo minimo candidato como definitivo
                            $this->datas[$bnMinActual]['min'] = $this->datas[$bnMinActual]['provMin'];
                            $this->datas[$bnMinActual]['trendBars'] = 1;

                            // Calcula trendbars
                            for ($b = $bnMinActual+1; $b <= $i; $b++)
                                $this->datas[$b]['trendBars'] = $this->datas[$b-1]['trendBars'] + 1;

                            // Actualiza swingbars
                            $this->calculaSwingSize($i, $bnMinActual, $bnMaxActual, 'MINIMO');

                            // Calcula pivotes
                            $this->calculaPivotes($bnMinActual, $this->datas[$bnMinActual]['min'], 0, 
                                $bnMinActual, $bnMaxActual, 'MINIMO');
                            break;
                        }
                    }
                }
            }

            // Cuenta los high y low del rango
            if ($i > $n)
            {
                for ($j = $i; $j >= $i - $n + 1; $j--)
                {
                    if ($this->datas[$j]['high'] == $maxRango)
                        $nroMax++;                  
                    if ($this->datas[$j]['low'] == $minRango)
                        $nroMin++;
                }
            }
        
            switch($trend)
            {
            case 0:
                if ($low == $minRango)
                {
                    $minimo = $low;
                    $bnMin = $i;
                    $trend = -1;
                    $pivot0 = $minimo;
                    $this->pivotes[] = $minimo;
                    $barras = 1;
                    
                    // Asigna valores
                    $this->datas[$i]['tendencia'] = $trend;
                    $this->datas[$i]['pivot0'] = $pivot0;
                    $this->datas[$i]['trendBars'] = $barras;
                    $this->datas[$i]['volumen'] = $volumen;
                }
                elseif ($high == $maxRango)
                {
                    $maximo = $high;
                    $bnMax = $i;
                    $pivot0 = $maximo;
                    $this->pivotes[] = $maximo;
                    $trend = 1;
                    $barras = 1;

                    // Asigna valores
                    $this->datas[$i]['tendencia'] = $trend;
                    $this->datas[$i]['pivot0'] = $pivot0;
                    $this->datas[$i]['trendBars'] = $barras;
                    $this->datas[$i]['volumen'] = $volumen;
                }
                break;
            case 1:
                if ($low == $minRango && $nroMin == 1)
                {
                    $minimo = $low;

                    // Asigna valores
                    $this->datas[$i]['volumen'] = $volumen;
                    $trend = -1;
                    $this->datas[$i]['tendencia'] = $trend;

                    $barras = 1;
                    $this->datas[$i]['trendBars'] = $barras;
                }

                if ($high >= $maximo && $trend == $this->datas[$i-1]['tendencia'])
                {
                    $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];
                    $maximo = $high;
                    $bnMax = $i;

                    $this->datas[$i]['tendencia'] = $trend;
                    $barras++;
                    $this->datas[$i]['trendBars'] = $barras;

                    if (count($this->pivotes) > 2)
                    {
                        $pivot0 = $this->pivotes[0]; $pivot1 = $this->pivotes[1]; $pivot2 = $this->pivotes[2];
                    }
                    if (isset($pivot0) && isset($pivot1) && isset($pivot2))
                    {
                        if ($pivot0 != 0 && $pivot1 != 0 && $pivot2 != 0)
                        {
                            $cswingProv = $pivot0 - $this->datas[$i]['provMax'];
                            $cswing = $pivot1 - $pivot2;
                            if ($cswing != 0)
                                $retrocesoProv = ABS($cswingProv / $cswing);
                            else    
                                $retrocesoProv = 0;
                            $this->datas[$i]['provRet'] = $retrocesoProv;
                        }
                    }
                }
                else
                {
                    if ($trend == $this->datas[$i-1]['tendencia'])
                    {
                        $barras++;
                        $this->datas[$i]['trendBars'] = $barras;
                        $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];
                    }
                    $this->datas[$i]['tendencia'] = $trend;
                }
                break;
            case -1:
                if ($high == $maxRango && $nroMax == 1)
                {
                    $maximo = $high;
                    $this->datas[$i]['volumen'] = $volumen;
                    //$this->datas[$i]['max'] = $maximo;
                    //$this->datas[$i]['provMax'] = $maximo;
                    $trend = 1;
                    $this->datas[$i]['tendencia'] = $trend;
                    $barras = 1;
                    $this->datas[$i]['trendBars'] = $barras;
                }

                if ($low <= $minimo && $trend == $this->datas[$i-1]['tendencia'])
                {
                    $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];

                    $minimo = $low;

                    $this->datas[$i]['tendencia'] = $trend;
                    $barras++;
                    $this->datas[$i]['trendBars'] = $barras;
                    
                    if (count($this->pivotes) > 2)
                    {
                        $pivot0 = $this->pivotes[0]; $pivot1 = $this->pivotes[1]; $pivot2 = $this->pivotes[2];
                    }
                    if (isset($pivot0) && isset($pivot1) && isset($pivot2))
                    {
                        if ($pivot0 != 0 && $pivot1 != 0 && $pivot2 != 0)
                        {
                            $cswingProv = $pivot0 - $this->datas[$i]['provMin'];
                            $cswing = $pivot1 - $pivot2;
                            $retrocesoProv = ABS($cswingProv / $cswing);
                            $this->datas[$i]['provRet'] = $retrocesoProv;
                        }
                    }
                }
                else
                {
                    if ($trend == $this->datas[$i-1]['tendencia'])
                    {
                        $barras++;
                        $this->datas[$i]['trendBars'] = $barras;
                        $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];
                    }
                    $this->datas[$i]['tendencia'] = $trend;
                }
                break;
            }
            // Calcula barras si es batch
            if ($this->flBatch)
            {
                if ($this->datas[$i]['swingBars'] != 0)
                    $this->datas[$i]['barras'] = $this->datas[$i]['swingBars'];
                else
                {
                    if ($this->datas[$i-1]['swingBars'] != 0)
                        $this->datas[$i]['barras'] = 1;
                    else
                        $this->datas[$i]['barras'] = $this->datas[$i-1]['barras'] + 1;
                }
            }

            $nroMax = 0;
            $nroMin = 0;
        }
        if (!$this->flBatch)
        {
            for ($i = 1; $i < count($this->datas); $i++)
            {
                // Calcula las barras
                if ($this->datas[$i]['swingBars'] != 0)
                    $this->datas[$i]['barras'] = $this->datas[$i]['swingBars'];
                else
                {
                    if ($this->datas[$i-1]['swingBars'] != 0)
                        $this->datas[$i]['barras'] = 1;
                    else
                        $this->datas[$i]['barras'] = $this->datas[$i-1]['barras'] + 1;
                }
            }
        }
    }

    private function calculaSwingSize($i, $bnMin, $bnMax, $swing)
    {
        if ($bnMin != 0 && $bnMax != 0)
        {
            $swingBars = ABS($bnMax - $bnMin);

            if ($swing == 'MAXIMO')
            {
                $this->datas[$i]['swingBars'] = $swingBars;
                $this->datas[$i]['swingBarsPrev'] = $this->datas[$bnMax]['swingBars'];
            }
            else
            {
                $this->datas[$i]['swingBars'] = $swingBars;
                $this->datas[$i]['swingBarsPrev'] = $this->datas[$bnMin]['swingBars'];
            }
        }
    }

    private function calculaPivotes($i, $minimo, $maximo, $bnMin, $bnMax, $swing)
    {
        if ($bnMin != 0 && $bnMax != 0)
        {
            if ($swing == 'MINIMO')
                $off = $bnMin;
            else
                $off = $bnMax;

            $this->apilaPivotes($swing == 'MINIMO' ? $minimo : $maximo);

            for ($i = 0; $i < count($this->pivotes); $i++)
            {
                $key = 'pivot'.$i;

                $this->datas[$off][$key] = $this->pivotes[$i];
            }
        
            if (count($this->pivotes) >= 3)
            {
                $pivot0 = $this->pivotes[0]; $pivot1 = $this->pivotes[1]; $pivot2 = $this->pivotes[2];

                if ($pivot0 != 0 && $pivot1 != 0 && $pivot2 != 0)
                {
                    $cswingProv = $pivot0 - 
                                ($swing == 'MINIMO' ? $this->datas[$i]['provMax'] : $this->datas[$i]['provMin']);
                    $swing = $pivot1 - $pivot2;
                    $cswing = $pivot1 - $pivot0;
                    $retroceso = 0;
                    if ($swing != 0)
                        $retroceso = $cswing / $swing;
                    if ($cswing != 0)
                        $retrocesoProv = ABS($cswingProv / $cswing);
                    else
                        $retrocesoProv = 0;
                    $this->datas[$i]['provRet'] = $retrocesoProv;
                    $this->datas[$off]['retroceso'] = $retroceso;

                    if ($pivot1 > $pivot2)
                    {
                        $this->datas[$off]['extT1'] = $pivot0 + ABS($swing) * 0.618;
                        $this->datas[$off]['extT2'] = $pivot0 + ABS($swing) * 1;
                        $this->datas[$off]['extT3'] = $pivot0 + ABS($swing) * 1.618;
                        $this->datas[$off]['extT4'] = $pivot0 + ABS($swing) * 2.618;

                        if ($pivot0 > $pivot2)
                            $this->datas[$off]['setup'] = 'HL';
                        elseif ($pivot0 < $pivot2)
                            $this->datas[$off]['setup'] = 'LL';
                        else    
                            $this->datas[$off]['setup'] = 'DB';
                    }
                    elseif ($pivot1 < $pivot2)
                    {
                        $this->datas[$off]['extT1'] = $pivot0 - ABS($swing) * 0.618;
                        $this->datas[$off]['extT2'] = $pivot0 - ABS($swing) * 1;
                        $this->datas[$off]['extT3'] = $pivot0 - ABS($swing) * 1.618;
                        $this->datas[$off]['extT4'] = $pivot0 - ABS($swing) * 2.618;

                        if ($pivot0 > $pivot2)
                            $this->datas[$off]['setup'] = 'HL';
                        elseif ($pivot0 < $pivot2)
                            $this->datas[$off]['setup'] = 'LH';
                        elseif ($pivot0 == $pivot2)
                            $this->datas[$off]['setup'] = 'DT';
                    }
                }
            }
        }
    }

    private function apilaPivotes($valor)
    {
        if ($valor != $this->pivotes[0])
        {
            if (count($this->pivotes) > 4)
                $tope = 5;
            else    
                $tope = count($this->pivotes);
            for ($i = $tope; $i > 0; $i--)
            {
                $this->pivotes[$i] = $this->pivotes[$i-1];
            }
            $this->pivotes[0] = $valor;
        }
    }

    private function calculaPivotOriginal()
    {
        $trend = 0;
        $maxRango = 0;
        $minRango = 99999999999;
        $nroMin = $nroMax = 0;
        $bnMin = $bnMax = 0;
        $bnMinOriginal = 0;
        $pivot3 = $pivot2 = $pivot1 = 0;
        $cswing = 1;
        $flMax = false; $barrasDesdeMinimo = 0;
        $n = $this->swingSize;
        for ($i = $n - 1; $i < count($this->datas); $i++)
        {
            $maxRango = 0;
            $minRango = 999999999999;
            for ($j = $i - $n + 1; $j <= $i; $j++)
            {
                if ($this->datas[$j]['high'] > $maxRango)
                    $maxRango = $this->datas[$j]['high'];
                if ($this->datas[$j]['low'] < $minRango)
                    $minRango = $this->datas[$j]['low'];
            }

            $low = $this->datas[$i]['low'];
            $high = $this->datas[$i]['high'];
            $volumen = $this->datas[$i]['volume'];

            // Cuenta los high y low del rango
            if ($i > $n)
            {
                for ($j = $i; $j >= $i - $n + 1; $j--)
                {
                    if ($this->datas[$j]['high'] == $maxRango)
                        $nroMax++;                  
                    if ($this->datas[$j]['low'] == $minRango)
                        $nroMin++;
                }
            }
        
            switch($trend)
            {
            case 0:
                if ($low == $minRango)
                {
                    $minimo = $low;
                    $bnMin = $i;
                    $trend = -1;
                    $pivot0 = $minimo;
                    $barras = 1;
                    
                    // Asigna valores
                    $this->datas[$i]['provMin'] = $minimo;
                    $this->datas[$i]['min'] = $minimo;
                    $this->datas[$i]['tendencia'] = $trend;
                    $this->datas[$i]['pivot0'] = $pivot0;
                    $this->datas[$i]['trendBars'] = $barras;
                    $this->datas[$i]['volumen'] = $volumen;
                }
                elseif ($high == $maxRango)
                {
                    $maximo = $high;
                    $bnMax = $i;
                    $pivot0 = $maximo;
                    $trend = 1;
                    $barras = 1;

                    // Asigna valores
                    $this->datas[$i]['provMax'] = $maximo;
                    $this->datas[$i]['max'] = $maximo;
                    $this->datas[$i]['tendencia'] = $trend;
                    $this->datas[$i]['pivot0'] = $pivot0;
                    $this->datas[$i]['trendBars'] = $barras;
                    $this->datas[$i]['volumen'] = $volumen;
                }
                break;
            case 1:
                if ($low == $minRango && $nroMin == 1)
                {
                    $minimo = $low;

                    // Asigna valores
                    $this->datas[$i]['volumen'] = $volumen;
                    $this->datas[$i]['provMin'] = $minimo;
                    $this->datas[$i]['min'] = $minimo;
                    $trend = -1;
                    $this->datas[$i]['tendencia'] = $trend;

                    if ($bnMin != 0 && $bnMax != 0)
                    {
                        $swingBars = ABS($bnMin - $bnMax);
                        $this->datas[$bnMax]['swingBars'] = $swingBars;
                        $this->datas[$bnMax]['swingBarsPrev'] = $this->datas[$bnMin]['swingBars'];

                        $pivot4 = $pivot3;
                        $pivot3 = $pivot2;
                        $pivot2 = $pivot1;
                        $pivot1 = $pivot0;
                        $pivot0 = $maximo;

                        $this->datas[$bnMax]['pivot0'] = $pivot0;
                        $this->datas[$bnMax]['pivot1'] = $pivot1;
                        $this->datas[$bnMax]['pivot2'] = $pivot2;
                        $this->datas[$bnMax]['pivot3'] = $pivot3;
                        $this->datas[$bnMax]['pivot4'] = $pivot4;
                    
                        if (isset($pivot0) && isset($pivot1) and isset($pivot2))
                        {
                            if ($pivot0 != 0 && $pivot1 != 0 && $pivot2 != 0)
                            {
                                $cswingProv = $pivot0 - $this->datas[$i]['provMin'];
                                $swing = $pivot1 - $pivot2;
                                $cswing = $pivot1 - $pivot0;
                                $retrocesoProv = ABS($cswingProv / $cswing);
                                if ($swing != 0)
                                    $retroceso = $cswing / $swing;
                                else    
                                    $retroceso = 0;

                                // Asigna datos
                                $this->datas[$i]['provRet'] = $retrocesoProv;
                                $this->datas[$bnMax]['retroceso'] = $retroceso;

                                if ($pivot1 > $pivot2)
                                {
                                    $this->datas[$bnMax]['extT1'] = $pivot0 + ABS($swing) * 0.618;
                                    $this->datas[$bnMax]['extT2'] = $pivot0 + ABS($swing) * 1;
                                    $this->datas[$bnMax]['extT3'] = $pivot0 + ABS($swing) * 1.618;
                                    $this->datas[$bnMax]['extT4'] = $pivot0 + ABS($swing) * 2.618;

                                    if ($pivot0 > $pivot2)
                                        $this->datas[$bnMax]['setup'] = 'HL';
                                    elseif ($pivot0 < $pivot2)
                                        $this->datas[$bnMax]['setup'] = 'LL';
                                    elseif ($pivot0 == $pivot2)
                                        $this->datas[$bnMax]['setup'] = 'DB';
                                }
                                elseif ($pivot1 < $pivot2)
                                {
                                    $this->datas[$bnMax]['extT1'] = $pivot0 - ABS($swing) * 0.618;
                                    $this->datas[$bnMax]['extT2'] = $pivot0 - ABS($swing) * 1;
                                    $this->datas[$bnMax]['extT3'] = $pivot0 - ABS($swing) * 1.618;
                                    $this->datas[$bnMax]['extT4'] = $pivot0 - ABS($swing) * 2.618;

                                    if ($pivot0 < $pivot2)
                                        $this->datas[$bnMax]['setup'] = 'LH';
                                    elseif ($pivot0 > $pivot2)
                                        $this->datas[$bnMax]['setup'] = 'HH';
                                    elseif ($pivot0 == $pivot2)
                                        $this->datas[$bnMax]['setup'] = 'DT';
                                }
                            }
                        }
                    }
                    $bnMin = $i;
                    $barras = 1;
                    $this->datas[$i]['trendBars'] = $barras;
                }

                if ($high >= $maximo && $trend == $this->datas[$i-1]['tendencia'])
                {
                    $this->datas[$bnMax]['max'] = 0;
                    $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];
                    $maximo = $high;
                    $bnMax = $i;

                    // Asigna valores
                    $this->datas[$i]['provMax'] = $maximo;
                    //$this->datas[$i]['barras'] = ABS($bnMin - $bnMax);
                    $this->datas[$i]['max'] = $maximo;
                    $this->datas[$i]['tendencia'] = $trend;
                    $barras++;
                    $this->datas[$i]['trendBars'] = $barras;

                    if (isset($pivot1) && isset($pivot1) && isset($pivot2))
                    {
                        if ($pivot1 != 0 && $pivot1 != 0 && $pivot2 != 0)
                        {
                            $cswingProv = $pivot0 - $this->datas[$i]['provMax'];
                            $swing = $pivot1 - $pivot2;
                            if ($cswing != 0)
                                $retrocesoProv = ABS($cswingProv / $cswing);
                            else    
                                $retrocesoProv = 0;
                            $this->datas[$i]['provRet'] = $retrocesoProv;
                        }
                    }
                }
                else
                {
                    if ($trend == $this->datas[$i-1]['tendencia'])
                    {
                        $barras++;
                        $this->datas[$i]['trendBars'] = $barras;
                        $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];
                    }
                    $this->datas[$i]['tendencia'] = $trend;
                }
                break;
            case -1:
                if ($high == $maxRango && $nroMax == 1)
                {
                    $maximo = $high;
                    $this->datas[$i]['volumen'] = $volumen;
                    $this->datas[$i]['max'] = $maximo;
                    $this->datas[$i]['provMax'] = $maximo;
                    $trend = 1;
                    $this->datas[$i]['tendencia'] = $trend;
                    if ($bnMin != 0 && $bnMax != 0)
                    {
                        //$this->datas[$i]['barras'] = ABS($bnMin - $i);
                        $swingBars = ABS($bnMin - $bnMax);
                        $this->datas[$bnMin]['swingBars'] = $swingBars;
                        $this->datas[$bnMin]['swingBarsPrev'] = $this->datas[$bnMax]['swingBars'];
                      
                        $pivot4 = $pivot3;
                        $pivot3 = $pivot2;
                        $pivot2 = $pivot1;
                        $pivot1 = $pivot0;
                        $pivot0 = $minimo;
                    
                        $this->datas[$bnMin]['pivot0'] = $pivot0;
                        $this->datas[$bnMin]['pivot1'] = $pivot1;
                        $this->datas[$bnMin]['pivot2'] = $pivot2;
                        $this->datas[$bnMin]['pivot3'] = $pivot3;
                        $this->datas[$bnMin]['pivot4'] = $pivot4;
                    
                        if (isset($pivot0) && isset($pivot1) && isset($pivot2))
                        {
                            if ($pivot0 != 0 && $pivot1 != 0 && $pivot2 != 0)
                            {
                                $cswingProv = $pivot0 - $this->datas[$i]['provMax'];
                                $swing = $pivot1 - $pivot2;
                                $cswing = $pivot1 - $pivot0;
                                $retroceso = $cswing / $swing;
                                if ($cswing != 0)
                                    $retrocesoProv = ABS($cswingProv / $cswing);
                                else
                                    $retrocesoProv = 0;
                                $this->datas[$i]['provRet'] = $retrocesoProv;
                                $this->datas[$bnMin]['retroceso'] = $retroceso;

                                if ($pivot1 > $pivot2)
                                {
                                    $this->datas[$bnMin]['extT1'] = $pivot0 + ABS($swing) * 0.618;
                                    $this->datas[$bnMin]['extT2'] = $pivot0 + ABS($swing) * 1;
                                    $this->datas[$bnMin]['extT3'] = $pivot0 + ABS($swing) * 1.618;
                                    $this->datas[$bnMin]['extT4'] = $pivot0 + ABS($swing) * 2.618;

                                    if ($pivot0 > $pivot2)
                                        $this->datas[$bnMin]['setup'] = 'HL';
                                    elseif ($pivot0 < $pivot2)
                                        $this->datas[$bnMin]['setup'] = 'LL';
                                    else    
                                        $this->datas[$bnMin]['setup'] = 'DB';
                                }
                                elseif ($pivot1 < $pivot2)
                                {
                                    $this->datas[$bnMin]['extT1'] = $pivot0 - ABS($swing) * 0.618;
                                    $this->datas[$bnMin]['extT2'] = $pivot0 - ABS($swing) * 1;
                                    $this->datas[$bnMin]['extT3'] = $pivot0 - ABS($swing) * 1.618;
                                    $this->datas[$bnMin]['extT4'] = $pivot0 - ABS($swing) * 2.618;

                                    if ($pivot0 > $pivot2)
                                        $this->datas[$bnMin]['setup'] = 'HL';
                                    elseif ($pivot0 < $pivot2)
                                        $this->datas[$bnMin]['setup'] = 'LH';
                                    elseif ($pivot0 == $pivot2)
                                        $this->datas[$bnMin]['setup'] = 'DT';
                                }
                            }
                        }
                    }
                    $bnMax = $i;
                    $barras = 1;
                    $this->datas[$i]['trendBars'] = $barras;
                }
                $bnMinOriginal = $bnMin;

                if ($low <= $minimo && $trend == $this->datas[$i-1]['tendencia'])
                {
                    $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];

                    $minimo = $low;

                    // Busca ultimo minimo
                    for ($j = $bnMin, $flMax = false, $barrasDesdeMinimo = 0; $j > 0; $j--)
                    {
                        $barrasDesdeMinimo++;
                        if ($this->datas[$j]['max'] != 0.)
                            $flMax = true;
                        if ($this->datas[$j]['min'] != 0.)
                            break;
                    }

                    $this->datas[$i]['estado'] = 'low '.$low.' minimo '.$minimo.' trend '.$trend.
                    ' tendencia '.$this->datas[$i-1]['tendencia'].' minrango '.$minRango.' bar '.$barrasDesdeMinimo.
                    ' bnmin '.$bnMin.' i '.$i.' min BNMIN '.$this->datas[$bnMin]['min'];

                    if ($barrasDesdeMinimo < $this->swingSize || !$flMax)
                    {
                        $this->datas[$i]['estado'] .= ' entro';
                        $this->datas[$bnMin]['min'] = 0;
                        $bnMin = $i;
                        $this->datas[$i]['min'] = $minimo;
                    }

                    $this->datas[$i]['provMin'] = $minimo;
                    //$this->datas[$i]['barras'] = ABS($bnMin - $bnMax);
                    $this->datas[$i]['tendencia'] = $trend;
                    $barras++;
                    $this->datas[$i]['trendBars'] = $barras;

                    if (isset($pivot0) && isset($pivot1) && isset($pivot2))
                    {
                        if ($pivot0 != 0 && $pivot1 != 0 && $pivot2 != 0)
                        {
                            $cswingProv = $pivot0 - $this->datas[$i]['provMin'];
                            $swing = $pivot1 - $pivot2;
                            $retrocesoProv = ABS($cswingProv / $cswing);
                            $this->datas[$i]['provRet'] = $retrocesoProv;
                        }
                    }
                }
                else
                {
                    if ($trend == $this->datas[$i-1]['tendencia'])
                    {
                        $barras++;
                        $this->datas[$i]['trendBars'] = $barras;
                        $this->datas[$i]['volumen'] = $volumen + $this->datas[$i-1]['volumen'];
                    }
                    $this->datas[$i]['tendencia'] = $trend;
                }
                break;
            }

            // Calcula barras si es batch
            if ($this->flBatch)
            {
                if ($this->datas[$i]['swingBars'] != 0)
                    $this->datas[$i]['barras'] = $this->datas[$i]['swingBars'];
                else
                {
                    if ($this->datas[$i-1]['swingBars'] != 0)
                        $this->datas[$i]['barras'] = 1;
                    else
                        $this->datas[$i]['barras'] = $this->datas[$i-1]['barras'] + 1;
                }
            }

            $nroMax = 0;
            $nroMin = 0;
        }
        if (!$this->flBatch)
        {
            for ($i = 1; $i < count($this->datas); $i++)
            {
                // Calcula las barras
                if ($this->datas[$i]['swingBars'] != 0)
                    $this->datas[$i]['barras'] = $this->datas[$i]['swingBars'];
                else
                {
                    if ($this->datas[$i-1]['swingBars'] != 0)
                        $this->datas[$i]['barras'] = 1;
                    else
                        $this->datas[$i]['barras'] = $this->datas[$i-1]['barras'] + 1;
                }
            }
        }
    }

    private function calculaSwingTgt()
    {
        $n = $this->swingSize;
        $flBuscaEntrada = $flAbrePosicion = false;
        $off0 = $off1oA = -1;
        $flAlcista = false;
        $flBajista = false;
        $flAbc = false;
        $flAbCd = false;
        $flAnulacionAlcistaActiva = false;
        $flAnulacionBajistaActiva = false;
        $idSenial = $idTrade = 0;
        $this->cantidadActivaContratos = $this->totalContratos;
        for ($i = $n - 1; $i < count($this->datas); $i++)
        {
            $fechaActual = date('Y-m-d', ceil($this->datas[$i]['fecha']/1000));
            if ($fechaActual < "2023-03-12" || $fechaActual >= "2023-11-05")
                $flDayLight = true;
            else
            {
                if ($fechaActual >= "2023-03-12" && $fechaActual < "2023-11-05")
                    $flDayLight = false;
            }

            $pivot = $this->datas[$i]['swingBars'];
            $volumen = $this->datas[$i]['volume'];
            if ($pivot == 0 && $i > 0)
                $this->datas[$i]['volumenPorSwing'] = $volumen + $this->datas[$i-1]['volumenPorSwing'];
            else    
                $this->datas[$i]['volumenPorSwing'] = $volumen;

            // Calcula zona
            $zonaFinal = 'NN ';
            if ($this->datas[$i]['provRet'] != 0)
            {
                $zonas = [];
                $zonas[] = $this->calculaZona($this->datas[$i]['open'], $i);
                $zonas[] = $this->calculaZona($this->datas[$i]['high'], $i);
                $zonas[] = $this->calculaZona($this->datas[$i]['low'], $i);
                $zonas[] = $this->calculaZona($this->datas[$i]['close'], $i);
            
                foreach ($zonas as $zona)
                {
                    if ($zona != 'NN ')
                        $zonaFinal = $zona;
                }
            }
            $this->datas[$i]['zona'] = $zonaFinal;

            // Chequea por fin de anulacion por ABC/AB=CD
            if (($flAnulacionAlcistaActiva) && !$flAbrePosicion)
            {
                if ($fl3Drives)
                    $flAnulacionAlcistaActiva = Self::verificaAnulacionActiva3Drives($i, 'BAJISTA');

                if ($flShark)
                    $flAnulacionAlcistaActiva = Self::verificaAnulacionActivaShark($i, 'BAJISTA');

                if ($flW4)
                    $flAnulacionAlcistaActiva = Self::verificaAnulacionActivaW4($i, 'BAJISTA');

                if ($flSp)
                    $flAnulacionAlcistaActiva = Self::verificaAnulacionActivaSp($i, 'BAJISTA');
                else
                    $flAnulacionAlcistaActiva = Self::verificaAnulacionActiva($i, 'BAJISTA');
            }

            if ($flAnulacionBajistaActiva && !$flAbrePosicion)
            {
                if ($fl3Drives)
                    $flAnulacionBajistaActiva = Self::verificaAnulacionActiva3Drives($i, 'ALCISTA');

                if ($flShark)
                    $flAnulacionBajistaActiva = Self::verificaAnulacionActivaShark($i, 'ALCISTA');

                if ($flW4)
                    $flAnulacionBajistaActiva = Self::verificaAnulacionActivaW4($i, 'ALCISTA');

                if ($flSp)
                    $flAnulacionBajistaActiva = Self::verificaAnulacionActivaSp($i, 'ALCISTA');
                else
                    $flAnulacionBajistaActiva = Self::verificaAnulacionActiva($i, 'ALCISTA');
            }

            // Si tiene posicion abierta chequea contra ordenes hijas SL y PT
            if ($flAbrePosicion)
            {
                $this->datas[$i]['e'] = $open;
                $this->datas[$i]['t1'] = $this->datas[$offAbrePosicion]['t1'];
                $this->datas[$i]['t2'] = $this->datas[$offAbrePosicion]['t2'];
                $this->datas[$i]['t3'] = $this->datas[$offAbrePosicion]['t3'];
                $this->datas[$i]['t4'] = $this->datas[$offAbrePosicion]['t4'];
                $this->datas[$i]['p'] = '1';
                $this->datas[$i]['evento'] = $this->datas[$offAbrePosicion]['evento'];

                // Redefine si es alcista o bajista
                if ($this->datas[$i]['evento'] == 'Compra')
                {
                    $flAlcista = true;
                    $flBajista = false;
                }
                else
                {
                    $flAlcista = false;
                    $flBajista = true; 
                }

                if ($flAlcista)
                    $this->datas[$i]['stoploss'] = $stopLoss - $this->ticker;
                if ($flBajista)
                    $this->datas[$i]['stoploss'] = $stopLoss + $this->ticker;

                // Controla si cumple eventos de cierre (TGT Hit / SL)
                $this->controlaCierreTgtSl($i, $flAlcista, $flBajista, $flAbrePosicion);

                // Chequea cierre de posicion por fuera de hora (NO MERCADO)
                if ($flAbrePosicion)
                {
                    if ($this->datas[$i]['horainicio'] >= ($flDayLight ? '18:00:00' : '17:00:00'))
                    {
                        $this->datas[$i]['p'] = '0';
                        $this->datas[$i]['evento'] = 'NM';
                        $flAbrePosicion = false;
                    }
                }

                // Si esta en tgt hit y tiene mas contratos cambia el SL
                if (substr($this->datas[$i]['evento'], 0, 7) == 'Tgt Hit' && $this->cantidadActivaContratos > 0 &&
                    $this->totalContratos > 1)
                {
                    $contratoActivo = $this->totalContratos - $this->cantidadActivaContratos + 1;
                    if ($contratoActivo == 2) // Si estoy en el 2do. contrato activo
                        $stopLoss = ($flAlcista ? $this->datas[$offAbrePosicion]['e']: 
                                                    $this->datas[$offAbrePosicion]['e']);
                    else // Si no se mueve al target anterior
                        $stopLoss = ($flAlcista ? $this->tgt[$contratoActivo-2] - $this->ticker : 
                                                    $this->tgt[$contratoActivo-2] + $this->ticker);
                    $this->datas[$i]['entrada'] = 'Mueve SL por alcanzar TGT Contrato activo='.$contratoActivo.
                                                    ' Contratos restantes='.$this->cantidadActivaContratos.
                                                    ' nuevo SL '.$stopLoss.' TGT contrato='.$this->tgt[$contratoActivo];
                }
                // Chequea para cerrar swing
                if (!$flAbrePosicion)
                {
            		// Si hay mas de 1 contrato continua abierta la posicion
                	if ($this->cantidadActivaContratos > 0 && substr($this->datas[$i]['evento'], 0, 7) == 'Tgt Hit')
                		$flAbrePosicion = true;
                    else
                    {
                        $off1oA = -1;
                        $this->cierraPosicion($i, $flAlcista, $flBajista, $offAbrePosicion,
                    						$idSenial, $idTrade, $this->cantidadActivaContratos, $tipoOperacion);
                    }
                }
			}

            // Sigue una vela mas con la entrada habilitada para disparar gatillo 
            if ($flBuscaEntrada)
            {
                // calcula riesgo retorno
                if ($flAlcista)
                {
                    $retorno = $t1 - $this->datas[$i]['open'];
                    $riesgo = $this->datas[$i]['open'] - $stopLoss;
                    if ($riesgo != 0)
                        $rrr = $retorno / $riesgo;
                    else   
                        $rrr = 0;
                }
                else
                {
                    if ($flBajista)
                    {
                        $retorno = $this->datas[$i]['open'] - $t1;
                        $riesgo = $stopLoss - $this->datas[$i]['open'];
                        $rrr = abs($retorno) / abs($riesgo);
                    }
                }

                $this->datas[$i]['entrada'] = ' Retorno '.$retorno.' Riesgo '.$riesgo.' RRR '.$rrr.' SL '.$stopLoss;
                if ($rrr >= 1.5 && 
                    $this->datas[$i]['horainicio'] >= '04:00:00' &&
                    $this->datas[$i]['horainicio'] <= ($flDayLight ? '17:00:00' : '16:00:00'))
                {
                    // Redondea T1
                    //$t1 = redondear($t1, 2, $this->ticker*100);

                    $open = $this->datas[$i]['open'];

                    if (!$flAbrePosicion)
                    {
                        $flAbrePosicion = true;
                        $this->datas[$i]['e'] = $open;
                        $this->datas[$i]['stoploss'] = ($flAlcista ? $stopLoss - $this->ticker : $stopLoss + $this->ticker);
                        $this->datas[$i]['t1'] = $t1;
                        $this->datas[$i]['t2'] = $t2;
                        $this->datas[$i]['t3'] = $t3;
                        $this->datas[$i]['t4'] = $t4;
                        $this->datas[$i]['p'] = '1';
                        $this->datas[$i]['evento'] = ($flAlcista ? 'Compra' : 'Vende');
                        $offAbrePosicion = $i;
						$this->buscaUltimoPivot($i);
                        $this->cantidadActivaContratos = $this->totalContratos;

                        // Arma tabla de operaciones
                        $riesgoPuntos = abs($open-($flAlcista ? $stopLoss - $this->ticker : $stopLoss + $this->ticker));
                        $riesgoTicks = round($riesgoPuntos/$this->ticker, 0);
                        $riesgoPesos = $riesgoTicks * $this->valorTicker;
                        $retornoPuntos = abs($open-$t1);
                        $retornoTicks = round($retornoPuntos/$this->ticker, 0);
                        $retornoPesos = $retornoTicks * $this->valorTicker;
                        if ($riesgoTicks != 0)
                            $rrr = $retornoTicks / $riesgoTicks;

                        $tipoOperacion = ($flAlcista ? "Buy to Open" : "Sell to Open");
                        $this->armaTablaOperaciones($idSenial, ++$idTrade, $this->cantidadActivaContratos, "Gatillo operación", 
                                                    $tipoOperacion,
                                                    $this->datas[$i]['fechastr'], $this->datas[$i]['horainicio'], 
                                                    $this->datas[$i]['fecha'],
                                                    '', '', '', '', '', '', '',
                                                    $open, 
                                                    '', '', '', '', 
                                                    '', '',
                                                    $riesgoPuntos, $riesgoTicks, $riesgoPesos,
                                                    $retornoPuntos, $retornoTicks, $retornoPesos, $rrr, 
                                                    '', '', '', '', '', '', '', '');
                    }
                    else
                    {
                        $stopLoss = $open;
                        $this->datas[$i]['stoploss'] = ($flAlcista ? $stopLoss - $this->ticker : $stopLoss + $this->ticker);
                        $this->datas[$i]['entrada'] .= 'BE por SP contrario '.$this->datas[$i]['stoploss'];

                        // Normaliza flags de sentido del setup
                        if ($flAlcista)
                        {
                            $flAlcista = false;
                            $flBajista = true;
                        }
                        else
                        {
                            $flAlcista = true;
                            $flBajista = false;
                        }
                    }
                }
                else
                {
                    // Si es un pivot analiza nuevamente
                    if ($this->datas[$i]['provRet'] >= 0.5 && $this->datas[$i]['provRet'] <= 1)
                    {
                        $flAbrePosicion = false;
                        $this->datas[$i]['entrada'] .= ' Encuentra un nuevo pivot';
                    }
                    else
                    {
                        $flAbrePosicion = false;
                    }
                    $this->armaTablaOperaciones($idSenial, '', '', "Filtrado por RRR", "",
                                                $this->datas[$i]['fechastr'], $this->datas[$i]['horainicio'], 
                                                $this->datas[$i]['fecha'],
                                                '', '', '', '', '', '', '',
                                                '', 
                                                $this->datas[$i]['stoploss'], $this->datas[$i]['t1'], 
                                                '', '', 
                                                '', '',
                                                '', '', '',
                                                '', '', '', '', 
                                                '', '', '', '', '', '', '', '');
                }

                $flBuscaEntrada = false;
            }

            // Si no esta con posicion abierta va chequeando cada vela para ver si encuentra setup
            // solo de los puntos candidatos
            if (($this->filtroSetup != 'T' ? !$flAbrePosicion : true) &&
                $this->datas[$i]['provRet'] >= 0.5 && $this->datas[$i]['provRet'] <= 1 &&
                $this->datas[$i]['horainicio'] >= '04:00:00' &&
                $this->datas[$i]['horainicio'] <= ($flDayLight ? '17:00:00' : '16:00:00'))
            {
                $minimoActual = $this->datas[$i]['low'];
                $maximoActual = $this->datas[$i]['high'];

                // Define si el candidato es alcista o bajista 
                $off0 = $off1oA = -1;
                
                if (!$flAbrePosicion)
                    $flBajista = $flAlcista = false;

                $retroceso = $relacionVelas = 0;
                if ($minimoActual == $this->datas[$i]['provMin'] && 
    				!$flAnulacionAlcistaActiva &&
                    ($this->filtroSetup == 'A' || $this->filtroSetup == 'T')) // Alcista
                {
                    if (!$flAbrePosicion)
                        self::buscaMinMaxAlcista($i, $off1oA, $off0, $stopLoss, $maximo1oA);

                    // Si viene con posicion abierta en mismo sentido descarta
                    if ($flAbrePosicion && $flAlcista) 
                        $off0 = $off1oA = -1;
                    else
                    {
                        $flBajista = false;
                        $flAlcista = true;
                    }

                    // Si obtiene maximo y minimo calcula valores para verificar gatillo
                    if ($off1oA != -1 && $off0 != -1)
                    {
                        $flAnulacionAlcistaActiva = false;

                        // Si esta en un punto maximo o minimo busca criterios de anulacion
                        if ($this->datas[$i]['max'] != 0)
                            $this->calculaFiltros($i, $flAnulacionAlcistaActiva, 
                                                $flAnulacionBajistaActiva, 'BAJISTA');

                        if (!$flAnulacionAlcistaActiva)
                        {
                            $recorrido1oA = $maximo1oA - $stopLoss;
                            $recorrido2oB = $maximo1oA - $minimoActual;
                            $retroceso = $recorrido2oB / $recorrido1oA;

                            $barras1oA = $off1oA - $off0;
                            $barras2oB = $i - $off1oA;
                            $relacionVelas = $barras2oB / $barras1oA;

                            $t1 = ($recorrido1oA * 0.618) + $minimoActual;
                            $t2 = ($recorrido1oA) + $minimoActual;
                            $t3 = ($recorrido1oA * 1.618) + $minimoActual;
                            $t4 = ($recorrido1oA * 2.618) + $minimoActual;
                        }
                        else
                            $off0 = $off1oA = -1; // Descarta
                    }
                }
                if ($maximoActual == $this->datas[$i]['provMax'] &&
                    !$flAnulacionBajistaActiva &&
                    ($this->filtroSetup == 'B' || $this->filtroSetup == 'T')) // Bajista
                {
                    if (!$flAbrePosicion)
                        self::buscaMinMaxBajista($i, $off1oA, $off0, $stopLoss, $minimo);
                    
                    // Si viene con posicion abierta en mismo sentido descarta
                    if ($flAbrePosicion && $flBajista) 
                        $off0 = $off1oA = -1;
                    else
                    {
                        $flBajista = true;
                        $flAlcista = false;
                    }

                    // Si obtiene maximo y minimo calcula valores para verificar gatillo
                    if ($off1oA != -1 && $off0 != -1)
                    {
                        $flAnulacionBajistaActiva = false;

                        // Si esta en un punto maximo o minimo busca criterios de anulacion
                        if ($this->datas[$i]['min'] != 0)
                            $this->calculaFiltros($i, $flAnulacionAlcistaActiva, 
                                                    $flAnulacionBajistaActiva, 'ALCISTA');

                        if (!$flAnulacionBajistaActiva)
                        {
                            $recorrido1oA = $stopLoss - $minimo;
                            $recorrido2oB = $maximoActual - $minimo;
                            $retroceso = $recorrido2oB / $recorrido1oA;

                            $barras1oA = $off0 - $off1oA;
                            $barras2oB = $i - $off0;
                            $relacionVelas = $barras2oB / $barras1oA;

                            $t1 = $maximoActual - ($recorrido1oA * 0.618);
                            $t2 = $maximoActual - ($recorrido1oA * 1.618);
                            $t3 = $maximoActual - ($recorrido1oA);
                            $t4 = $maximoActual - ($recorrido1oA * 2.618);
                        }
                        else
                            $off0 = $off1oA = -1; // Descarta
                    }
                }
                
                // Si obtiene maximo y minimo verifica gatillo
                if ($off1oA != -1 && $off0 != -1)
                {
                    if ($retroceso >= 0.50 && $relacionVelas <= 1)
                    {
                        $flBuscaEntrada = true;

                        $this->datas[$i]['entrada'] .= ' Retroceso '.$retroceso.' RV '.$relacionVelas.' T1 '.$t1;

                        if (!$flAbrePosicion)
                        {
                            $zonaOpen = $this->calculaZona($this->datas[$i]['open'], $i);
                            $zonaHigh = $this->calculaZona($this->datas[$i]['high'], $i);
                            $zonaLow = $this->calculaZona($this->datas[$i]['low'], $i);
                            $zonaClose = $this->calculaZona($this->datas[$i]['close'], $i);

                            $this->armaTablaOperaciones(++$idSenial, '', '', 
                                                        ($flBajista ? "Señal de venta" : "Señal de compra"), "",
                                                        $this->datas[$i]['fechastr'], $this->datas[$i]['horainicio'], 
                                                        $this->datas[$i]['fecha'],
                                                        $zonaOpen, $zonaHigh, $zonaLow, $zonaClose, 
                                                        $this->datas[$i]['ewo'], $this->datas[$i]['bandaSup'],
                                                        $this->datas[$i]['bandaInf'], 0, 
                                                        ($flAlcista ? $stopLoss - $this->ticker : $stopLoss + $this->ticker),
                                                        $t1, 
                                                        $this->datas[$i]['swingBarsPrev'], 
                                                        $this->datas[$i]['barras'], 
                                                        $relacionVelas, $retroceso, 
                                                        
                                                        '', '', '', '', '', '', '',
                                                        '', '', '', '', '', '', '', '');
                        }
                    }
                }
            }
            $sp = $this->datas[$i]['setup'];
            if ($sp != '')
            {
                $t1 = $this->datas[$i]['extT1'];
                $t2 = $this->datas[$i]['extT2'];
                $t3 = $this->datas[$i]['extT3'];
                $t4 = $this->datas[$i]['extT4'];
                $t1Hit = $t2Hit = $t3Hit = $t4Hit = 0;

                switch($sp)
                {
                case 'HL':
                case 'LL':
                case 'DB':
                    $columna = 'high';
                    break;
                case 'LH':
                case 'HH':
                case 'DT':
                    $columna = 'low';
                    break;
                }

                for ($j = $i + 1; $j < count($this->datas); $j++)
                {
                    $spCtrl = $this->datas[$j]['setup'];
                    $extremo = $this->datas[$j][$columna];
                    if ($columna == 'high')
                    {
                        if ($t1Hit == 0 && $t1 <= $extremo)
                        {
                            $t1Hit = 1;
                            $this->datas[$i]['t1Hit'] = $j - $i;
                        }
                        elseif ($t2Hit == 0 && $t2 <= $extremo)
                        {
                            $t2Hit = 1;
                            $this->datas[$i]['t2Hit'] = $j - $i;
                        }
                        elseif ($t3Hit == 0 && $t3 <= $extremo)
                        {
                            $t3Hit = 1;
                            $this->datas[$i]['t3Hit'] = $j - $i;
                        }
                        elseif ($t4Hit == 0 && $t4 <= $extremo)
                        {
                            $t4Hit = 1;
                            $this->datas[$i]['t4Hit'] = $j - $i;
                        }
                    }
                    else
                    {
                        if ($columna == 'low')
                        {
                            if ($t1Hit == 0 && $t1 >= $extremo)
                            {
                                $t1Hit = 1;
                                $this->datas[$i]['t1Hit'] = $j - $i;
                            }
                            elseif ($t2Hit == 0 && $t2 >= $extremo)
                            {
                                $t2Hit = 1;
                                $this->datas[$i]['t2Hit'] = $j - $i;
                            }
                            elseif ($t3Hit == 0 && $t3 >= $extremo)
                            {
                                $t3Hit = 1;
                                $this->datas[$i]['t3Hit'] = $j - $i;
                            }
                            elseif ($t4Hit == 0 && $t4 >= $extremo)
                            {
                                $t4Hit = 1;
                                $this->datas[$i]['t4Hit'] = $j - $i;
                            }
                        }
                    }
                    if ($spCtrl != '')
                        break;
                }
            }
        }
    }

    private function calculaFiltros($i, &$flAnulacionAlcistaActiva, &$flAnulacionBajistaActiva, $op)
    {
        // Calcula filtros ABC / AB=CD
        $flAbc = self::calculaAbc($i, $op);
        $flAbCd = self::calculaAbCd($i, $op);
        $fl3Drives = self::calcula3Drives($i, $op);
        $flShark = self::calculaShark($i, $op);
        $flW4 = self::calculaW4($i, $op);
        $flSp = self::calculaSp($i, $op);

        if ($flAbc || $flAbCd || $flShark || $flW4 || $flSp)
        {
            if ($op == 'BAJISTA')
                $flAnulacionAlcistaActiva = true;
            else
                $flAnulacionBajistaActiva = true;

            $this->datas[$i]['entrada'] .= ' ACTIVA ANULACION de candidatos '.
                                            ($op == 'BAJISTA' ? 'alcistas' : 'bajistas');
        }
    }
    
    private function cierraPosicion($i, $flAlcista, $flBajista, $offAbrePosicion, 
                                    $idSenial, $idTrade, $cantidadActivaContratos, $tipoOperacion)
    {
        if ($this->datas[$i]['evento'] == 'NM')
            $precioCierre = $this->datas[$i]['close'];
        else
        {
            if ($this->datas[$i]['evento'] == 'SL')
                $precioCierre = $this->datas[$i]['stoploss'];
            else
                $precioCierre = round($this->datas[$i]['t1'] / $this->ticker, 0) * $this->ticker;
        }

        $plPuntos = ($this->datas[$i]['e'] - $precioCierre) * ($flBajista ? 1 : -1);
        $plTicks = $plPuntos / $this->ticker;
        $plPesos = $plTicks * $this->valorTicker;
        $mpc = $this->calculaMpc($offAbrePosicion, $i, $this->datas[$i]['evento'], $precioCierre);
        $mpf = $this->calculaMpf($offAbrePosicion, $i, $this->datas[$i]['evento'], $precioCierre);

		if ($this->datas[$i]['stoploss'] - $this->datas[$i]['e'] != 0)
        	$eficienciaEntrada = ($mpc - $this->datas[$i]['e']) / ($this->datas[$i]['stoploss'] - $this->datas[$i]['e']);
		else
        	$eficienciaEntrada = 0.;

		if ($this->datas[$i]['e']-$mpf != 0)
        	$eSalida = ($this->datas[$i]['e'] - $precioCierre) / ($this->datas[$i]['e']-$mpf);
		else
        	$eSalida = ($this->datas[$i]['e'] - $precioCierre);
        if ($eSalida < 0)
            $eficienciaSalida = 0;
        else
            $eficienciaSalida = $eSalida;
        
        $tipoOperacion = ($flAlcista ? "Sell to Close" : "Buy to Close");
        $this->armaTablaOperaciones($idSenial, $idTrade, $cantidadActivaContratos, $this->datas[$i]['evento'], 
            $tipoOperacion,
            $this->datas[$i]['fechastr'], $this->datas[$i]['horainicio'], 
            $this->datas[$i]['fecha'],
            '', '', '', '', '', '', '',
            '', '', '', '', '', '', '',
            '', '', '', '', '', '', '',
            $precioCierre,
            $plPuntos, $plTicks, $plPesos, $mpc, $mpf, $eficienciaEntrada*100,
            $eficienciaSalida*100);
    }

    private function controlaCierreTgtSl($i, $flAlcista, $flBajista, &$flAbrePosicion)
    {
        $contratoActivo = $this->totalContratos - $this->cantidadActivaContratos + 1;
        if ($flAlcista)
        {
            if ($this->datas[$i]['open'] >= $this->tgt[$contratoActivo] ||
                $this->datas[$i]['close'] >= $this->tgt[$contratoActivo] ||
                $this->datas[$i]['high'] >= $this->tgt[$contratoActivo] ||
                $this->datas[$i]['low'] >= $this->tgt[$contratoActivo])
            {
                $this->datas[$i]['evento'] = 'Tgt Hit '.$this->tgt[$contratoActivo].' Contrato activo '.$contratoActivo.'/'.
					$this->cantidadActivaContratos.' TOTAL '.$this->totalContratos;
                $this->cantidadActivaContratos--;
                if ($this->cantidadActivaContratos == 0)
				{
                	$this->datas[$i]['p'] = '0';
                    $flAbrePosicion = false;
				}
            }
            // Chequea con SL
            if ($this->datas[$i]['open'] <= $this->datas[$i]['stoploss'] ||
                $this->datas[$i]['close'] <= $this->datas[$i]['stoploss'] ||
                $this->datas[$i]['high'] <= $this->datas[$i]['stoploss'] ||
                $this->datas[$i]['low'] <= $this->datas[$i]['stoploss'])
            {
                $this->datas[$i]['p'] = '0';
                $this->datas[$i]['evento'] = 'SL '.$this->datas[$i]['stoploss'].' A i'.$i;
                $flAbrePosicion = false;
            }
        }
        if ($flBajista)
        {
            // Chequea Target 1
            if ($this->datas[$i]['open'] <= $this->tgt[$contratoActivo] ||
                $this->datas[$i]['close'] <= $this->tgt[$contratoActivo] ||
                $this->datas[$i]['high'] <= $this->tgt[$contratoActivo] ||
                $this->datas[$i]['low'] <= $this->tgt[$contratoActivo])
            {
                $this->datas[$i]['evento'] = 'Tgt Hit '.$this->tgt[$contratoActivo].' Contrato activo '.$contratoActivo.'/'.
					$this->cantidadActivaContratos.' TOTAL '.$this->totalContratos;
                $this->cantidadActivaContratos--;
                if ($this->cantidadActivaContratos == 0)
				{
                	$this->datas[$i]['p'] = '0';
                    $flAbrePosicion = false;
				}
            }
            // Chequea con SL
            if ($this->datas[$i]['open'] >= $this->datas[$i]['stoploss'] ||
                $this->datas[$i]['close'] >= $this->datas[$i]['stoploss'] ||
                $this->datas[$i]['high'] >= $this->datas[$i]['stoploss'] ||
                $this->datas[$i]['low'] >= $this->datas[$i]['stoploss'])
            {
                $this->datas[$i]['p'] = '0';
                $this->datas[$i]['evento'] = 'SL '.$this->datas[$i]['stoploss'].' B i'.$i;
                $flAbrePosicion = false;
            }
        }
    }

    private function buscaMinMaxAlcista($offset, &$off1oA, &$off0, &$stopLoss, &$maximo1oA)
    {
        for ($o = $offset; $o >= 0; $o--)
        {
            if ($this->datas[$o]['min'] != 0)
            {
                if ($off1oA != -1)
                {
                    $off0 = $o;
                    $stopLoss = $this->datas[$o]['low'];
                    if (!strstr($this->datas[$o]['entrada'], 'Stop Loss'))
                        $this->datas[$o]['entrada'] .= ' Nuevo STOP
                         Loss Alcista '.$stopLoss;
                }
            }
            if ($this->datas[$o]['max'] != 0)
            {
                $off1oA = $o;
                $vela1oA = $this->datas[$o]['high'] - $this->datas[$o]['low'];
                $maximo1oA = $this->datas[$o]['high'];
                $this->datas[$o]['entrada'] .= ' Maximo Vela 1oA '.$vela1oA.' Max. 1oA '.$maximo1oA;
            }
            if ($off1oA != -1 && $off0 != -1)
                break;
        }
    }

    private function buscaMinMaxBajista($offset, &$offmax, &$offmin, &$stopLoss, &$minimo)
    {
        for ($o = $offset; $o >= 0; $o--)
        {
            if ($this->datas[$o]['max'] != 0)
            {
                if ($offmin != -1)
                {
                    $offmax = $o;
                    $stopLoss = $this->datas[$o]['high'];
                    $this->datas[$o]['entrada'] .= ' Nuevo Stop Loss '.$minimo.' offset '.$o;
                    if (!strstr($this->datas[$o]['entrada'], 'Stop Loss Bajista '))
                        $this->datas[$o]['entrada'] .= ' Nuevo STOP Loss Bajista '.$stopLoss;
                }
            }
            if ($this->datas[$o]['min'] != 0)
            {
                $offmin = $o;
                $minimo = $this->datas[$o]['min'];
                $this->datas[$o]['entrada'] .= ' Nuevo Minimo Bajista '.$minimo;
            }
            if ($offmax != -1 && $offmin != -1)
                break;
        }
    }

    private function calculaZona($valor, $offset)
    {
        $zona = 'NN ';
        if ($valor > $this->datas[$offset]['rfeInt'] &&
            $valor < $this->datas[$offset]['rfeExt'])
            $zona = 'RFE';
        if ($valor > $this->datas[$offset]['rfiInt'] &&
            $valor < $this->datas[$offset]['rfiExt'])
            $zona = 'RFI';
        if ($valor > $this->datas[$offset]['pp2'] &&
            $valor < $this->datas[$offset]['pp1'])
            $zona = 'PoC';
        if ($valor > $this->datas[$offset]['sfiExt'] &&
            $valor < $this->datas[$offset]['sfiInt'])
            $zona = 'SFI';
        if ($valor > $this->datas[$offset]['sfeExt'] &&
            $valor < $this->datas[$offset]['sfeInt'])
            $zona = 'SFE';

        return $zona;
    }

    private function calculaMpc($offAbrePosicion, $offCierraPosicion, $evento, $precioCierre)
    {
        $mpc = 0;
        for ($i = $offAbrePosicion; $i <= $offCierraPosicion; $i++)   
        {
            // Calcula maximo de valores high mientras se tiene abierta la posicion
            if ($this->datas[$i]['high'] > $mpc)
                $mpc = $this->datas[$i]['high'];
        }
        if ($evento == 'SL' && $precioCierre < $mpc)
            $mpc = $precioCierre;
        return $mpc;
    }

    private function calculaMpf($offAbrePosicion, $offCierraPosicion, $evento, $precioCierre)
    {
        $mpf = 0;
        $minimo = 99999999999;
        if ($evento == 'SL')
        {
            $offAbrePosicion = 0; $offCierraPosicion = count($this->datas)-1;
        }
        for ($i = $offAbrePosicion; $i <= $offCierraPosicion; $i++)   
        {
            // Calcula maximo de valores high mientras se tiene abierta la posicion
            if ($this->datas[$i]['low'] < $minimo && $this->datas[$i]['low'] != 0)
                $minimo = $this->datas[$i]['low'];
        }
        if ($evento != 'SL')
        {
            if ($minimo > $precioCierre)
                $mpf = $minimo;
            else    
                $mpf = $precioCierre;
        }
        else    
            $mpf = $minimo;
        return $mpf;
    }

    private function calculaAbc($offset, $setup)
    {
        if ($setup == 'BAJISTA')
        {
            $offMin = 1;
            $offMax = 2;
        }
        else
        {
            $offMin = 2;
            $offMax = 1;
        }
        $D = 2; $C = 1; $B = 1; $A = 0; $O = 0;
        $min = array();
        $max = array();
        for ($o = $offset; $o >= 0 && ($offMin >= 0 || $offMax >= 0); $o--)
        {
            if ($this->datas[$o]['min'] != 0 && $offMin >= 0)
                $min[$offMin--] = $o;

            if ($this->datas[$o]['max'] != 0 && $offMax >= 0)
                $max[$offMax--] = $o;
        }
        // Si encontro los minimos y maximos necesarios calcula cibducuibes de ABC
        $condicion0 = $condicion1 = $condicion2 = $condicion3 = 0;
        $retroceso1 = $retroceso2 = 0;
        $this->datas[$offset]['entrada'] .= "CONTROL ABC INICIAL ".$offMin." ".$offMax." ".$setup;
        if ($offMin == -1 && $offMax == -1)
        {
            if ($setup == 'BAJISTA')
            {
                if ($this->datas[$max[$D]]['max'] > $this->datas[$max[$B]]['max'] && 
                    $this->datas[$max[$D]]['max'] < $this->datas[$max[$O]]['max'] &&
                    $this->datas[$min[$C]]['min'] >= $this->datas[$min[$A]]['min'])
                    $condicion0 = true;

                if (abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']))
                    $retroceso1 = abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']) / 
                                abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']);

                if (abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']))
                    $retroceso2 = abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']) / 
                            abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']);

                $barras2 = $this->datas[$max[$D]]['swingBars'];
                $barras1 = $this->datas[$max[$B]]['swingBars'];
            }
            else
            {
                if ($this->datas[$min[$D]]['min'] < $this->datas[$min[$B]]['min'] && 
                    $this->datas[$min[$D]]['min'] < $this->datas[$min[$O]]['min'] &&
                    $this->datas[$max[$C]]['max'] < $this->datas[$max[$A]]['max'])
                    $condicion0 = true;

                if (abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']))
                    $retroceso1 = abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']) / 
                                abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']);

                if (abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']))
                    $retroceso2 = abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']) / 
                            abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']);

                $barras2 = $this->datas[$min[$D]]['swingBars'];
                $barras1 = $this->datas[$min[$B]]['swingBars'];
            }

            // Arma condicion 1 2 y 3
            if ($retroceso1 >= 0.382 && $retroceso1 <= 0.886)
                $condicion1 = true;
                
            if ($retroceso2 >= 1.13 && $retroceso2 <= 2.618)
                $condicion2 = true;

            $cocienteBarras = 0;
            if ($barras1 != 0)
                $cocienteBarras = $barras2 / $barras1;

            if ($cocienteBarras >= 0.5 && $cocienteBarras <= 2)
                $condicion3 = true;

            if ($setup == 'BAJISTA')
                $this->datas[$offset]['entrada'] .= " Control ABC Max D=".$this->datas[$max[$D]]['max']." Min C=". 
												$this->datas[$min[$C]]['min']." Max B=".$this->datas[$max[$B]]['max'].
												" Min A=".$this->datas[$min[$A]]['min']." Max O=".$this->datas[$max[$O]]['max'].
                                                " Retroceso 1=".$retroceso1.
												" Retroceso 2=".$retroceso2." Barras DC ".$barras2." Barras BA ".$barras1." Cociente barras=".$cocienteBarras." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3;
            else
                $this->datas[$offset]['entrada'] .= " Control ABC Min D=".$this->datas[$min[$D]]['min']." Max C=". 
												$this->datas[$max[$C]]['max']." Min B=".$this->datas[$min[$B]]['min'].
												" Max A=".$this->datas[$max[$A]]['max']." Min O=".$this->datas[$min[$O]]['min'].
                                                " Retroceso 1=".$retroceso1.
												" Retroceso 2=".$retroceso2." Barras DC ".$barras2." Barras BA ".$barras1." Cociente barras=".$cocienteBarras." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3;
        }
        if ($condicion0 && $condicion1 && $condicion2 && $condicion3)
        {
            // Asigna variables globales
            if ($setup == 'BAJISTA')
            {
                $this->offsetMaximoD = $max[$D];
                $this->offsetMinimoC = $min[$C];
                $this->offsetMaximoB = $max[$B];
                $this->offsetMinimoA = $min[$A];
            }
            else
            {
                $this->offsetMinimoD = $min[$D];
                $this->offsetMaximoC = $max[$C];
                $this->offsetMinimoB = $min[$B];
                $this->offsetMaximoA = $max[$A];
            }

            // Marca aviso de punto ABC
            $this->datas[$offset]['entrada'] .= " CUMPLE ABC ".$setup." ";

            $this->offsetAbcd = $offset;
            return true;
        }
        return false;
    }

    private function calculaAbCd($offset, $setup)
    {
        $offMin = 1;
        $offMax = 1;
        $D = 1; $C = 1; $B = 0; $A = 0;
        for ($o = $offset; $o >= 0 && ($offMin >= 0 || $offMax >= 0); $o--)
        {
            if ($this->datas[$o]['min'] != 0 && $offMin >= 0)
                $min[$offMin--] = $o;

            if ($this->datas[$o]['max'] != 0 && $offMax >= 0)
                $max[$offMax--] = $o;
        }
        // Si encontro los minimos y maximos necesarios calcula cibducuibes de ABC
        $condicion0 = $condicion1 = $condicion2 = $condicion3 = 0;
        $retroceso1 = $retroceso2 = 0;
        $this->datas[$offset]['entrada'] .= "CONTROL AB=CD INICIAL ".$offMin." ".$offMax." ".$setup;
        if ($offMin == -1 && $offMax == -1)
        {
            if ($setup == 'BAJISTA')
            {
                if ($this->datas[$max[$D]]['max'] > $this->datas[$max[$B]]['max'] && 
                    $this->datas[$min[$C]]['min'] > $this->datas[$min[$A]]['min'])
                    $condicion0 = true;

                if (abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']) > 0)
                    $retroceso1 = abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']) / 
                            abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']);

                if (abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']) > 0)
                    $retroceso2 = abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']) / 
                            abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']);
                
                $barras2 = $this->datas[$max[$D]]['swingBars'];
                $barras1 = $this->datas[$max[$B]]['swingBars'];                
            }
            else
            {
                if ($this->datas[$min[$D]]['min'] < $this->datas[$min[$B]]['min'] && 
                    $this->datas[$max[$C]]['max'] < $this->datas[$max[$A]]['max'])
                    $condicion0 = true;

                if (abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']) > 0)
                    $retroceso1 = abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']) / 
                            abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']);

                if (abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']) > 0)
                    $retroceso2 = abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']) / 
                            abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']);

                $barras2 = $this->datas[$min[$D]]['swingBars'];
                $barras1 = $this->datas[$min[$B]]['swingBars'];
            }

            // Arma condicion 1 2 y 3
            if ($retroceso1 >= 0.382 && $retroceso1 <= 0.886)
                $condicion1 = true;

            if ($retroceso2 >= 1.13 && $retroceso2 <= 2.618)
                $condicion2 = true;

            $barras2 = $this->datas[$max[$D]]['swingBars'];
            $barras1 = $this->datas[$max[$B]]['swingBars'];

            $cocienteBarras = 0;
            if ($barras1 != 0)
                $cocienteBarras = $barras2 / $barras1;

            if ($cocienteBarras >= 0.5 && $cocienteBarras <= 2)
                $condicion3 = true;

            if ($setup == 'BAJISTA')
                $this->datas[$offset]['entrada'] .= " Control AB=CD Max D=".$this->datas[$max[$D]]['max']." Min C=". 
												$this->datas[$min[$C]]['min']." Max B=".$this->datas[$max[$B]]['max'].
												" Max A=".$this->datas[$min[$A]]['min']. " Retroceso 1=".$retroceso1.
												" Retroceso 2=".$retroceso2." Barras DC ".$barras2." Barras BA ".$barras1.
                                                " Cociente barras=".$cocienteBarras." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3;
            else            
                $this->datas[$offset]['entrada'] .= " Control AB=CD Min D=".$this->datas[$min[$D]]['min']." Max C=". 
                                                $this->datas[$max[$C]]['max']." Min B=".$this->datas[$min[$B]]['min'].
                                                " Max A=".$this->datas[$max[$A]]['max']. " Retroceso 1=".$retroceso1.
                                                " Retroceso 2=".$retroceso2." Barras DC ".$barras2." Barras BA ".$barras1.
                                                " Cociente barras=".$cocienteBarras." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3;
        }
        if ($condicion0 && $condicion1 && $condicion2 && $condicion3)
        {
            // Asigna variables globales
            if ($setup == 'BAJISTA')
            {
                $this->offsetMaximoD = $max[$D];
                $this->offsetMinimoC = $min[$C];
                $this->offsetMaximoB = $max[$B];
                $this->offsetMinimoA = $min[$A];
            }
            else
            {
                $this->offsetMinimoD = $min[$D];
                $this->offsetMaximoC = $max[$C];
                $this->offsetMinimoB = $min[$B];
                $this->offsetMaximoA = $max[$A];
            }

            // Marca aviso de punto AB = CD
            $this->datas[$offset]['entrada'] .= " CUMPLE AB = CD ".$setup." ";

            $this->offsetAbcd = $offset;
            return true;
        }
        return false;
    }

    private function calcula3Drives($offset, $setup)
    {
        $offMin = 2;
        $offMax = 2;
        $E = 2; $D = 2; $C = 1; $B = 1; $A = 0; $O = 0;
        $min = array();
        $max = array();
        for ($o = $offset; $o >= 0 && ($offMin >= 0 || $offMax >= 0); $o--)
        {
            if ($this->datas[$o]['min'] != 0 && $offMin >= 0)
                $min[$offMin--] = $o;

            if ($this->datas[$o]['max'] != 0 && $offMax >= 0)
                $max[$offMax--] = $o;
        }
        // Si encontro los minimos y maximos necesarios calcula cibducuibes de ABC
        $condicion0 = $condicion1 = $condicion2 = $condicion3 = $condicion4 = 0;
        $retroceso1 = $retroceso2 = $retroceso3 = $retroceso4 = 0;
        $this->datas[$offset]['entrada'] .= "CONTROL 3DRIVES INICIAL ".$offMin." ".$offMax." ".$setup;
        if ($offMin == -1 && $offMax == -1)
        {
            if ($setup == "BAJISTA")
            {
                if (abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']) != 0)
                    $retroceso1 = abs($this->datas[$min[$D]]['min'] - $this->datas[$max[$E]]['max']) / 
                                abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']);

                if (abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']) != 0)
                    $retroceso2 = abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']) / 
                            abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']);

                if (abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']) != 0)
                    $retroceso3 = abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']) / 
                            abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']);
        
                if (abs($this->datas[$min[$O]]['min'] - $this->datas[$max[$A]]['max']) != 0)
                    $retroceso4 = abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']) / 
                            abs($this->datas[$min[$O]]['min'] - $this->datas[$max[$A]]['max']);

                $barras1 = $this->datas[$max[$A]]['swingBars'];
                $barras2 = $this->datas[$min[$B]]['swingBars'];
                $barras3 = $this->datas[$max[$C]]['swingBars'];
                $barras4 = $this->datas[$min[$D]]['swingBars'];
                $barras5 = $this->datas[$max[$E]]['swingBars'];
            }
            else
            {
                if (abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']) != 0)
                    $retroceso1 = abs($this->datas[$max[$D]]['max'] - $this->datas[$min[$E]]['min']) / 
                                abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']);

                if (abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']) != 0)
                    $retroceso2 = abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']) / 
                            abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']);

                if (abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']) != 0)
                    $retroceso3 = abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']) / 
                            abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']);
        
                if (abs($this->datas[$max[$O]]['max'] - $this->datas[$min[$A]]['min']) != 0)
                    $retroceso4 = abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']) / 
                            abs($this->datas[$max[$O]]['max'] - $this->datas[$min[$A]]['min']);
            
                $barras1 = $this->datas[$min[$A]]['swingBars'];
                $barras2 = $this->datas[$max[$B]]['swingBars'];
                $barras3 = $this->datas[$min[$C]]['swingBars'];
                $barras4 = $this->datas[$max[$D]]['swingBars'];
                $barras5 = $this->datas[$min[$E]]['swingBars'];
            }

            // Arma condicion 1 2 3 4 y 5
            if ($retroceso1 >= 1.1 && $retroceso1 <= 2)
                $condicion0 = true;
                
            if ($retroceso2 >= 0.382 && $retroceso2 <= 0.886)
                $condicion1 = true;

            if ($retroceso3 >= 1.1 && $retroceso3 <= 2)
                $condicion2 = true;

            if ($retroceso4 >= 0.382 && $retroceso4 <= 0.886)
                $condicion3 = true;

            if ($setup == 'BAJISTA')
            {
                if ($this->datas[$max[$A]]['max'] < $this->datas[$min[$D]]['min'] && 
                    $this->datas[$min[$D]]['min'] < $this->datas[$min[$B]]['min'] &&
                    $this->datas[$max[$B]]['max'] < $this->datas[$min[$O]]['min'])
                  $condicion4 = true;

                $this->datas[$offset]['entrada'] .= " Control 3Drives Max E=".$this->datas[$max[$E]]['max']." Min D=".$this->datas[$min[$D]]['min']." Max C=". 
                                            $this->datas[$max[$C]]['max']." Min B=".$this->datas[$min[$B]]['min'].
                                            " Max A=".$this->datas[$max[$A]]['max']." Min O=".$this->datas[$min[$O]]['min'].
                                            " Retroceso 1=".$retroceso1.
                                            " Retroceso 2=".$retroceso2." Barras 0A ".$barras1." Barras AB ".$barras2.
                                            " Barras BC ".$barras3." Barras CD ".$barras4." Barras DE ".$barras5." ".
                                            $condicion0." ".$condicion1." ".$condicion2." ".$condicion3." ".$condicion4;
            }
            else
            {
                if ($this->datas[$min[$A]]['min'] < $this->datas[$max[$D]]['max'] && 
                    $this->datas[$max[$D]]['max'] < $this->datas[$max[$B]]['max'] &&
                    $this->datas[$min[$B]]['min'] < $this->datas[$max[$O]]['max'])
                    $condicion4 = true;

                $this->datas[$offset]['entrada'] .= " Control 3Drives Min E=".$this->datas[$min[$E]]['min']." Max D=".$this->datas[$max[$D]]['max']." Min C=". 
												$this->datas[$min[$C]]['min']." Max B=".$this->datas[$max[$B]]['max'].
												" Min A=".$this->datas[$min[$A]]['min']." Max O=".$this->datas[$max[$O]]['max'].
                                                " Retroceso 1=".$retroceso1.
												" Retroceso 2=".$retroceso2." Barras 0A ".$barras1." Barras AB ".$barras2.
                                                " Barras BC ".$barras3." Barras CD ".$barras4." Barras DE ".$barras5." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3." ".$condicion4;
            }
        }
        if ($condicion0 && $condicion1 && $condicion2 && $condicion3 && $condicion4)
        {
            // Asigna variables globales
            if ($setup == 'BAJISTA')
            {
                $this->offsetMaximoE = $max[$E];
                $this->offsetMinimoD = $min[$D];
                $this->offsetMaximoC = $max[$C];
                $this->offsetMinimoB = $min[$B];
                $this->offsetMaximoA = $max[$A];
            }
            else
            {
                $this->offsetMinimoE = $min[$E];
                $this->offsetMaximoD = $max[$D];
                $this->offsetMinimoC = $min[$C];
                $this->offsetMaximoB = $max[$B];
                $this->offsetMinimoA = $min[$A];
            }

            // Marca aviso de punto ABC
            $this->datas[$offset]['entrada'] .= " CUMPLE 3Drives ".$setup." ";

            $this->offset3Drives = $offset;
            return true;
        }
        return false;
    }

    private function calculaShark($offset, $setup)
    {
        if ($setup == 'BAJISTA')
        {
            $offMin = 1;
            $offMax = 2;
        }
        else
        {
            $offMin = 2;
            $offMax = 1;
        }
        $D = 2; $C = 1; $B = 1; $A = 0; $O = 0;
        $min = array();
        $max = array();
        for ($o = $offset; $o >= 0 && ($offMin >= 0 || $offMax >= 0); $o--)
        {
            if ($this->datas[$o]['min'] != 0 && $offMin >= 0)
                $min[$offMin--] = $o;

            if ($this->datas[$o]['max'] != 0 && $offMax >= 0)
                $max[$offMax--] = $o;
        }
        // Si encontro los minimos y maximos necesarios calcula cibducuibes de ABC
        $condicion0 = $condicion1 = $condicion2 = $condicion3 = $condicion4 = 0;
        $retroceso1 = $retroceso2 = 0;
        $this->datas[$offset]['entrada'] .= "CONTROL 3DRIVES INICIAL ".$offMin." ".$offMax." ".$setup;
        if ($offMin == -1 && $offMax == -1)
        {
            if ($setup == 'BAJISTA')
            {
                if (abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']) != 0)
                    $retroceso1 = abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$D]]['max']) / 
                                abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']);

                $topeInferior = ($this->datas[$max[$O]]['max']-
                                abs($this->datas[$max[$O]]['max']-$this->datas[$min[$A]]['min']))*0.13;

                $topeSuperior = ($this->datas[$max[$O]]['max']+
                                abs($this->datas[$max[$O]]['max']-$this->datas[$min[$A]]['min']))*0.114;

                if (abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']) != 0)
                    $retroceso3 = abs($this->datas[$max[$B]]['max'] - $this->datas[$min[$C]]['min']) / 
                            abs($this->datas[$min[$A]]['min'] - $this->datas[$max[$B]]['max']);

                $barras1 = $this->datas[$min[$A]]['swingBars'];
                $barras2 = $this->datas[$max[$B]]['swingBars'];
                $barras3 = $this->datas[$min[$C]]['swingBars'];
                $barras4 = $this->datas[$max[$D]]['swingBars'];

                // Arma condicion 1 2 3 4 y 5
                if ($retroceso1 >= 1.618 && $retroceso1 <= 2.24)
                    $condicion0 = true;
                    
                if ($this->datas[$max[$D]]['max'] > $topeInferior &&
                    $this->datas[$max[$D]]['max'] < $topeSuperior)
                    $condicion1 = true;

                if ($retroceso3 >= 1.001 && $retroceso3 <= 1.618)
                    $condicion2 = true;

                if ($this->datas[$max[$B]]['max'] > $this->datas[$max[$O]]['max'])
                    $condicion3 = true;
            }
            else
            {
                if (abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']) != 0)
                    $retroceso1 = abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$D]]['min']) / 
                                abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']);

                $topeInferior = ($this->datas[$min[$O]]['min']-
                                abs($this->datas[$min[$O]]['min']-$this->datas[$max[$A]]['max']))*0.13;

                $topeSuperior = ($this->datas[$min[$O]]['min']+
                                abs($this->datas[$min[$O]]['min']-$this->datas[$max[$A]]['max']))*0.114;

                if (abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']) != 0)
                    $retroceso3 = abs($this->datas[$min[$B]]['min'] - $this->datas[$max[$C]]['max']) / 
                            abs($this->datas[$max[$A]]['max'] - $this->datas[$min[$B]]['min']);

                $barras1 = $this->datas[$max[$A]]['swingBars'];
                $barras2 = $this->datas[$min[$B]]['swingBars'];
                $barras3 = $this->datas[$max[$C]]['swingBars'];
                $barras4 = $this->datas[$min[$D]]['swingBars'];

                // Arma condicion 1 2 3 4 y 5
                if ($retroceso1 >= 1.618 && $retroceso1 <= 2.24)
                    $condicion0 = true;
                    
                if ($this->datas[$min[$D]]['min'] > $topeInferior &&
                    $this->datas[$min[$D]]['min'] < $topeSuperior)
                    $condicion1 = true;

                if ($retroceso3 >= 1.001 && $retroceso3 <= 1.618)
                    $condicion2 = true;

                if ($this->datas[$min[$B]]['min'] > $this->datas[$min[$O]]['min'])
                    $condicion3 = true;
            }

            if ($barras4 <= (0.618 * ($barras1+$barras2+$barras3)))
                $condicion4 = true;

            if ($setup == 'BAJISTA')
                $this->datas[$offset]['entrada'] .= " Control Shark Max D=".$this->datas[$max[$D]]['max']." Min C=". 
                                                $this->datas[$min[$C]]['min']." Max B=".$this->datas[$max[$B]]['max'].
                                                " Min A=".$this->datas[$min[$A]]['min']." Max O=".$this->datas[$max[$O]]['max'].
                                                " Retroceso 1=".$retroceso1.
                                                " Retroceso 3=".$retroceso3." Barras 0A ".$barras1." Barras AB ".$barras2.
                                                " Barras BC ".$barras3." Barras CD ".$barras4." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3." ".$condicion4;
            else
                $this->datas[$offset]['entrada'] .= " Control Shark Min D=".$this->datas[$min[$D]]['min']." Max C=". 
												$this->datas[$max[$C]]['max']." Min B=".$this->datas[$min[$B]]['min'].
												" Max A=".$this->datas[$max[$A]]['max']." Min O=".$this->datas[$min[$O]]['min'].
                                                " Retroceso 1=".$retroceso1.
												" Retroceso 3=".$retroceso3." Barras 0A ".$barras1." Barras AB ".$barras2.
                                                " Barras BC ".$barras3." Barras CD ".$barras4." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3." ".$condicion4;
        }
        if ($condicion0 && $condicion1 && $condicion2 && $condicion3 && $condicion4)
        {
            // Asigna variables globales
            if ($setup == 'BAJISTA')
            {
                $this->offsetMaximoD = $max[$D];
                $this->offsetMinimoC = $min[$C];
                $this->offsetMaximoB = $max[$B];
                $this->offsetMinimoA = $min[$A];
            }
            else
            {
                $this->offsetMinimoD = $min[$D];
                $this->offsetMaximoC = $max[$C];
                $this->offsetMinimoB = $min[$B];
                $this->offsetMaximoA = $max[$A];
            }

            // Marca aviso de punto ABC
            $this->datas[$offset]['entrada'] .= " CUMPLE Shark ".$setup." ";

            $this->offsetShark = $offset;
            return true;
        }
        return false;
    }

    private function calculaW4($offset, $setup)
    {
        if ($setup == 'BAJISTA')
        {
            $offMax = 2;
            $offMin = 1;
        }
        else
        {
            $offMax = 1;
            $offMin = 2;
        }
        $D = 2; $C = 1; $B = 1; $A = 0; $O = 0;
        $min = array();
        $max = array();
        for ($o = $offset; $o >= 0 && ($offMin >= 0 || $offMax >= 0); $o--)
        {
            if ($this->datas[$o]['min'] != 0 && $offMin >= 0)
                $min[$offMin--] = $o;

            if ($this->datas[$o]['max'] != 0 && $offMax >= 0)
                $max[$offMax--] = $o;
        }
        // Si encontro los minimos y maximos necesarios calcula condiciones
        $condicion0 = $condicion1 = $condicion2 = $condicion3 = 0;
        $retroceso1 = $retroceso3 = 0;
        $this->datas[$offset]['entrada'] .= "CONTROL W4 INICIAL ".$offMin." ".$offMax." ".$setup;
        if ($offMin == -1 && $offMax == -1)
        {
            if ($setup == 'ALCISTA')
            {
                $retroceso1 = abs($this->datas[$min[$O]]['min'] - $this->datas[$max[$A]]['max']);
                $retroceso3 = abs($this->datas[$max[$C]]['max'] - $this->datas[$min[$B]]['min']);
                
                $barras1 = $this->datas[$max[$A]]['swingBars'];
                $barras2 = $this->datas[$min[$B]]['swingBars'];
                $barras3 = $this->datas[$max[$C]]['swingBars'];
                $barras4 = $this->datas[$min[$D]]['swingBars'];

                // Arma condicion 1 2 3 4 y 5
                if ($this->datas[$min[$D]]['min'] > $this->datas[$max[$A]]['max'])
                    $condicion0 = true;
                    
                if ($this->datas[$min[$B]]['min'] > $this->datas[$min[$O]]['min'])
                    $condicion1 = true;
            }
            else
            {
                $retroceso1 = abs($this->datas[$max[$O]]['max'] - $this->datas[$min[$A]]['min']);
                $retroceso3 = abs($this->datas[$min[$C]]['min'] - $this->datas[$max[$B]]['max']);
                
                $barras1 = $this->datas[$min[$A]]['swingBars'];
                $barras2 = $this->datas[$max[$B]]['swingBars'];
                $barras3 = $this->datas[$min[$C]]['swingBars'];
                $barras4 = $this->datas[$max[$D]]['swingBars'];

                // Arma condicion 1 2 3 4 y 5
                if ($this->datas[$max[$D]]['max'] < $this->datas[$min[$A]]['min'])
                    $condicion0 = true;
                    
                if ($this->datas[$max[$B]]['max'] < $this->datas[$max[$O]]['max'])
                    $condicion1 = true;
            }
                
            if ($retroceso3 >= $retroceso1 * 1.5)
                $condicion2 = true;

            if ($this->datas[$offset]['ewo'] >= $this->datas[$offset]['w4Dw1'] &&
                $this->datas[$offset]['ewo'] <= $this->datas[$offset]['w4Dw2'])
                $condicion3 = true;

            if ($setup == 'ALCISTA')
                $this->datas[$offset]['entrada'] .= " Control W4 Min D=".$this->datas[$min[$D]]['min']." Max C=". 
                                                $this->datas[$max[$C]]['max']." Min B=".$this->datas[$min[$B]]['min'].
                                                " Max A=".$this->datas[$max[$A]]['max']." Min O=".$this->datas[$min[$O]]['min'].
                                                " Retroceso 1=".$retroceso1.
                                                " Retroceso 3=".$retroceso3." EWO=".$this->datas[$offset]['ewo'].
                                                " W4dw1=".$this->datas[$offset]['w4Dw1']." W4dw2=".$this->datas[$offset]['w4Dw2'].
                                                " Barras 0A ".$barras1." Barras AB ".$barras2.
                                                " Barras BC ".$barras3." Barras CD ".$barras4." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3;
            else
                $this->datas[$offset]['entrada'] .= " Control W4 Max D=".$this->datas[$max[$D]]['max']." Min C=". 
												$this->datas[$min[$C]]['min']." Max B=".$this->datas[$max[$B]]['max'].
												" Min A=".$this->datas[$min[$A]]['min']." Max O=".$this->datas[$max[$O]]['max'].
                                                " Retroceso 1=".$retroceso1.
												" Retroceso 3=".$retroceso3." EWO=".$this->datas[$offset]['ewo'].
                                                " W4dw1=".$this->datas[$offset]['w4Dw1']." W4dw2=".$this->datas[$offset]['w4Dw2'].
                                                " Barras 0A ".$barras1." Barras AB ".$barras2.
                                                " Barras BC ".$barras3." Barras CD ".$barras4." ".
                                                $condicion0." ".$condicion1." ".$condicion2." ".$condicion3;
        }
        if ($condicion0 && $condicion1 && $condicion2 && $condicion3)
        {
            // Asigna variables globales
            if ($setup == 'ALCISTA')
            {
                $this->offsetMinimoD = $min[$D];
                $this->offsetMaximoC = $max[$C];
                $this->offsetMinimoB = $min[$B];
                $this->offsetMaximoA = $max[$A];
            }
            else
            {
                $this->offsetMaximoD = $max[$D];
                $this->offsetMinimoC = $min[$C];
                $this->offsetMaximoB = $max[$B];
                $this->offsetMinimoA = $min[$A];
            }            

            // Marca aviso de punto 
            $this->datas[$offset]['entrada'] .= " CUMPLE W4 ".$setup." ";

            $this->offsetW4 = $offset;
            return true;
        }
        return false;
    }

    private function calculaSp($offset, $setup)
    {
        if ($setup == 'ALCISTA')
        {
            $offMin = 1;
            $offMax = 0;
        }
        else
        {
            $offMin = 0;
            $offMax = 1;
        }
        $B = 1; $A = 0; $O = 0;
        $min = array();
        $max = array();
        for ($o = $offset; $o >= 0 && ($offMin >= 0 || $offMax >= 0); $o--)
        {
            if ($this->datas[$o]['min'] != 0 && $offMin >= 0)
                $min[$offMin--] = $o;

            if ($this->datas[$o]['max'] != 0 && $offMax >= 0)
                $max[$offMax--] = $o;
        }
        // Si encontro los minimos y maximos necesarios calcula cibducuibes de ABC
        $condicion0 = 0;
        $this->datas[$offset]['entrada'] .= "CONTROL SP INICIAL ".$offMin." ".$offMax." ".$setup;
        if ($offMin == -1 && $offMax == -1)
        {
            if ($setup == 'ALCISTA')
            {
                if ($this->datas[$min[$B]]['min'] >= $this->datas[$min[$O]]['min'])
                    $condicion0 = true;
            }
            else
            {
                if ($this->datas[$max[$B]]['max'] <= $this->datas[$max[$O]]['max'])
                    $condicion0 = true;
            }

            if ($setup == 'ALCISTA')
                $this->datas[$offset]['entrada'] .= " Control SP ".$setup." Min B=".$this->datas[$min[$B]]['min'].
												" Max A=".$this->datas[$max[$A]]['max']." Min O=".$this->datas[$min[$O]]['min'].
                                                $condicion0;
            else
                $this->datas[$offset]['entrada'] .= " Control SP ".$setup." Max B=".$this->datas[$max[$B]]['max'].
                                                " Min A=".$this->datas[$min[$A]]['min']." Max O=".$this->datas[$max[$O]]['max'].
                                                $condicion0;
        }
        if ($condicion0)
        {
            // Asigna variables globales
            if ($setup == 'ALCISTA')
            {
                $this->offsetMinimoB = $min[$B];
                $this->offsetMaximoA = $max[$A];
            }
            else
            {
                $this->offsetMaximoB = $max[$B];
                $this->offsetMinimoA = $min[$A];
            }

            // Marca aviso de punto ABC
            $this->datas[$offset]['entrada'] .= " CUMPLE SP ".$setup." ";

            $this->offsetSP = $offset;
            return true;
        }
        return false;
    }

    private function verificaAnulacionActiva($offset, $setup)
    {
        $flSigueAnulacion = true;
        if ($setup == 'BAJISTA' && $this->offsetMaximoD != null && $this->offsetMinimoC != null)
        {
            $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos alcistas maximo D ".
                                            $this->datas[$this->offsetMaximoD]['high']." minimo C ".
                                            $this->datas[$this->offsetMinimoC]['low'];
            if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoD]['high'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo D ";
                $flSigueAnulacion = false;
            }

            if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoC]['low'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo C ";
                $flSigueAnulacion = false;
            }
        }
        else
        {
            if ($this->offsetMaximoD != null && $this->offsetMinimoC != null)
            {
                $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos bajistas minimo D ".
                                                $this->datas[$this->offsetMinimoD]['low']." maximo C ".
                                                $this->datas[$this->offsetMaximoC]['high'];
                if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoD]['low'])
                {
                    $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo D ";
                    $flSigueAnulacion = false;
                }

                if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoC]['high'])
                {
                    $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo C ";                                        
                    $flSigueAnulacion = false;
                }
            }
        }

        // Chequea ultima condicion para activar la anulacion
        if ($flSigueAnulacion && $this->offsetMaximoD != null && $this->offsetMinimoC != null)
        {
            // Calcula tiempo
            $tiempoSwing = ($this->datas[$this->offsetMaximoB]['swingBars'] +
                            $this->datas[$this->offsetMinimoC]['swingBars'] +
                            $this->datas[$this->offsetMaximoD]['swingBars']);

            $barrasDesdeAbcd = $offset - $this->offsetAbcd;

            $this->datas[$offset]['entrada'] .= " tiempo ".$tiempoSwing." offset ".$offset." Barras ".$barrasDesdeAbcd;
            if ($offset > $barrasDesdeAbcd)
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion tiempo ".$barrasDesdeAbcd.' '.$tiempoSwing." ";
                $flSigueAnulacion = false;
            }
        }
        return $flSigueAnulacion;
    }

    private function verificaAnulacionActiva3Drives($offset, $setup)
    {
        $flSigueAnulacion = true;
        if ($setup == 'BAJISTA')
        {
        }
        else
        {
            $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos bajistas minimo E ".
                                            $this->datas[$this->offsetMinimoE]['low']." maximo D ".
                                            $this->datas[$this->offsetMaximoD]['high'];
            if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoE]['low'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo E ";
                $flSigueAnulacion = false;
            }

            if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoD]['high'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo D ";                                        
                $flSigueAnulacion = false;
            }
        }

        // Chequea ultima condicion para activar la anulacion
        if ($flSigueAnulacion)
        {
            // Calcula tiempo
            $tiempoSwing = ($this->datas[$this->offsetMinimoA]['swingBars'] +
                            $this->datas[$this->offsetMaximoB]['swingBars'] +
                            $this->datas[$this->offsetMinimoC]['swingBars'] +
                            $this->datas[$this->offsetMaximoD]['swingBars'] +
                            $this->datas[$this->offsetMinimoE]['swingBars']);

            $barrasDesde3Drives = $this->offset3Drives + $tiempoSwing;

            $this->datas[$offset]['entrada'] .= " tiempo ".$tiempoSwing." offset ".$offset." Barras ".$barrasDesde3Drives;
            if ($offset > $barrasDesde3Drives)
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion tiempo ".$barrasDesde3Drives.' '.$tiempoSwing." ";
                $flSigueAnulacion = false;
            }
        }
        return $flSigueAnulacion;
    }

    private function verificaAnulacionActivaShark($offset, $setup)
    {
        $flSigueAnulacion = true;
        if ($setup == 'BAJISTA')
        {
        }
        else
        {
            $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos bajistas minimo D ".
                                            $this->datas[$this->offsetMinimoD]['low']." maximo B ".
                                            $this->datas[$this->offsetMaximoB]['high'];
            if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoD]['low'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo D ";
                $flSigueAnulacion = false;
            }

            if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoB]['high'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo B ";                                        
                $flSigueAnulacion = false;
            }
        }

        // Chequea ultima condicion para activar la anulacion
        if ($flSigueAnulacion)
        {
            // Calcula tiempo
            $tiempoSwing = ($this->datas[$this->offsetMinimoA]['swingBars'] * 2);

            $barrasDesdeShark = $this->offsetShark + $tiempoSwing;

            $this->datas[$offset]['entrada'] .= " tiempo ".$tiempoSwing." offset ".$offset.
                                                " Barras ".$barrasDesdeShark;
            if ($offset > $barrasDesdeShark)
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion tiempo ".$barrasDesdeShark.' '.$tiempoSwing." ";
                $flSigueAnulacion = false;
            }
        }
        return $flSigueAnulacion;
    }

    private function verificaAnulacionActivaW4($offset, $setup)
    {
        $flSigueAnulacion = true;
        if ($setup == 'ALCISTA')
        {
        }
        else
        {
            $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos alcistas minimo D ".
                                            $this->datas[$this->offsetMinimoC]['low']." maximo B ".
                                            $this->datas[$this->offsetMaximoD]['high'];
            if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoC]['low'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo C ";
                $flSigueAnulacion = false;
            }

            if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoD]['high'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo D ";                                        
                $flSigueAnulacion = false;
            }
        }

        // Chequea ultima condicion para activar la anulacion
        if ($flSigueAnulacion)
        {
            // Calcula tiempo
            $barrasDesdeW4 = $this->offsetW4 + 42;

            $this->datas[$offset]['entrada'] .= " tiempo 42 offset ".$offset.
                                                " Barras ".$barrasDesdeW4;
            if ($offset > $barrasDesdeW4)
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion tiempo ".$barrasDesdeW4;
                $flSigueAnulacion = false;
            }
        }
        return $flSigueAnulacion;
    }

    private function verificaAnulacionActivaSP($offset, $setup)
    {
        $flSigueAnulacion = true;
        if ($setup == 'ALCISTA')
        {
            $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos bajistas minimo B ".
                                            $this->datas[$this->offsetMinimoB]['low']." maximo A ".
                                            $this->datas[$this->offsetMaximoA]['high'];
            if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoB]['low'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo B ";
                $flSigueAnulacion = false;
            }

            if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoA]['high'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo A ";                                        
                $flSigueAnulacion = false;
            }
        }
        else
        {
            $this->datas[$offset]['entrada'] .= " Controla anulacion candidatos alcistas maximo B ".
                                            $this->datas[$this->offsetMaximoB]['high']." minimo A ".
                                            $this->datas[$this->offsetMinimoA]['low'];
            if ($this->datas[$offset]['low'] < $this->datas[$this->offsetMinimoA]['low'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion minimo A ";
                $flSigueAnulacion = false;
            }

            if ($this->datas[$offset]['high'] > $this->datas[$this->offsetMaximoB]['high'])
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion maximo B ";                                        
                $flSigueAnulacion = false;
            }
        }

        // Chequea ultima condicion para activar la anulacion
        if ($flSigueAnulacion)
        {
            // Calcula tiempo
            $barrasDesdeSp = $this->offsetSp + 64;

            $this->datas[$offset]['entrada'] .= " tiempo 64 offset ".$offset.
                                                " Barras ".$barrasDesdeSp;
            if ($offset > $barrasDesdeSp)
            {
                $this->datas[$offset]['entrada'] .= " Desactiva anulacion tiempo ".$barrasDesdeSp;
                $flSigueAnulacion = false;
            }
        }
        return $flSigueAnulacion;
    }

    private function lindev($item, $precioTipico, $largo, &$retorno)
    {
        for ($ii = $item - $largo, $cant = 0, $x = 0; $ii < $item - 1; $ii++)
        {
            $x += $this->datas[$ii]['precioTipico'];
            $cant++;
        }
        $x += $precioTipico;
        $cant++;
        $x /= ($cant > 0 ? $cant : 1);
        
        for ($ii = $item - $largo, $y = 0; $ii < $item-1; $ii++)
            $y += ABS($x - $this->datas[$ii]['precioTipico']);
        $y += ABS($x - $precioTipico);
        
        $retorno = $y / ($cant > 0 ? $cant : 1);
    }

    private function promedio($base, $item, $indice, $mm)
    {
        for ($ii = $item - $mm, $cant = 0, $acum = 0; $ii < $item - 1; $ii++)
        {
            $acum += $this->datas[$ii][$indice];
            $cant++;
        }
		// Agrega el item actual
        $acum += $base;
		$cant++;

        return $acum / ($cant > 0 ? $cant : 1);
    }

    private function acumulado($base, $item, $indice, $mm)
    {
        for ($ii = $item - $mm, $acum = 0; $ii < $item - 1; $ii++)
            $acum += $this->datas[$ii][$indice];

        // Agrega el item actual
        $acum += $base;

        return $acum;
    }

    private function calculaDatosDiaAnterior($fecha, &$open, &$close, &$low, &$high)
    {
        if ($fecha != $this->fechaUltimaLectura)
        {
            $count = 0;
            $fechaAnt = date("d-m-Y",strtotime($fecha."- 2 days"));    
            $fechaAct = date("d-m-Y",strtotime($fecha."- 1 days"));    
            $desde_fecha = strtotime($fechaAnt.' '.'19:00')*1000;
            $hasta_fecha = strtotime($fechaAct.' '.'18:00')*1000;

            do
            {
                $count++;

                $this->dataAnterior = DB::connection('trade')->table('trade.lecturas')
                    ->select('fechaChar as fechastr',
                            'chartTime as fecha',
                            'openPrice as open',
                            'highPrice as high',
                            'lowPrice as low',
                            'closePrice as close',
                            'volume')
                    ->where('especie', $this->especie)
                    ->whereBetween('chartTime', [$desde_fecha, $hasta_fecha])
                    ->get();

                if (count($this->dataAnterior) == 0)
                {
                    $fechaAnt = date("d-m-Y",strtotime($fechaAnt."- 1 days"));    
                    $fechaAct = date("d-m-Y",strtotime($fechaAct."- 1 days"));    
                    $desde_fecha = strtotime($fechaAnt.' '.'19:00')*1000;
                    $hasta_fecha = strtotime($fechaAct.' '.'18:00')*1000;
                }
            } while (count($this->dataAnterior) == 0 && $count < 10);
            $this->fechaUltimaLectura = $fecha;
        }
     
        $open = $close = $low = $high = 0;
        foreach ($this->dataAnterior as $lectura)
        {
            if ($open == 0)
            {
                $open = $lectura->open;
                $low = $lectura->low;
                $high = $lectura->high;
            }

            $close = $lectura->close;

            if ($lectura->low < $low)
                $low = $lectura->low;

            if ($lectura->high > $high)
                $high = $lectura->high;
        }
    }

    // Arma tabla para envio a impresion
    private function armaTabla($fechaStr, $fecha, $horaInicio, $open, $close, $low, $high, $totVolume, $ewo,
                                $bandaSup, $bandaInf, $w4Up1, $w4Up2, $w4Dw1, $w4Dw2,
                                $rfLim, $rfeExt, $rfeInt, $rfiExt, $rfiInt, $pp1, $poc, $pp2,
                                $sfiInt, $sfiExt, $sfeInt, $sfeExt, $sfLim, $base, $smac, $smal, $tmp1, $tmp2, $d1, $d2,
                                $condicional, $d3, $k, $VMA, $precioTipico, $SMACCI, $auxCCI, $blanco1, $blanco2,
                                $CCI, $SMAXTL, $auxXTL, $CCIXTL, $estado, $rango, $TQRVerde, $stopTQRVerde, $tgtTQRVerde,
                                $TQRRojo, $stopTQRRojo, $tgtTQRRojo)
    {
        $this->datas[] = ['fechastr'=>$fechaStr, 'fecha'=>$fecha, 'horainicio'=>$horaInicio,
            'open'=>$open, 'close'=>$close,
            'low'=>$low,'high'=>$high,'volume'=>$totVolume,
            'ewo'=>$ewo,
            'bandaSup'=>$bandaSup,
            'bandaInf'=>$bandaInf,
            'w4Up1'=>$w4Up1,
            'w4Up2'=>$w4Up2,
            'w4Dw1'=>$w4Dw1,
            'w4Dw2'=>$w4Dw2,
            'rfLim'=>$rfLim,
            'rfeExt'=>$rfeExt,
            'rfeInt'=>$rfeInt,
            'rfiExt'=>$rfiExt,
            'rfiInt'=>$rfiInt,
            'pp1'=>$pp1,
            'poc'=>$poc,
            'pp2'=>$pp2,
            'sfiInt'=>$sfiInt,
            'sfiExt'=>$sfiExt,
            'sfeInt'=>$sfeInt,
            'sfeExt'=>$sfeExt,
            'sfLim'=>$sfLim,
            'base'=>$base,
            'smac'=>$smac,
            'smal'=>$smal,
            'tmp1'=>$tmp1,
            'tmp2'=>$tmp2,
            'd1'=>$d1,
            'd2'=>$d2,
            'condicional'=>$condicional,
            'd3'=>$d3,
            'k'=>$k,
            'VMA'=>$VMA,
            'precioTipico'=>$precioTipico,
            'SMACCI'=>$SMACCI,
            'auxCCI'=>$auxCCI,
            'blanco1'=>$blanco1,
            'blanco2'=>$blanco2,
            'CCI'=>$CCI,
            'SMAXTL'=>$SMAXTL,
            'auxXTL'=>$auxXTL,
            'CCIXTL'=>$CCIXTL,
            'estado'=>$estado,
            'rango'=>$rango,
            'TQRVerde'=>$TQRVerde,
            'stopTQRVerde'=>$stopTQRVerde,
            'tgtTQRVerde'=>$tgtTQRVerde,
            'TQRRojo'=>$TQRRojo,
            'stopTQRRojo'=>$stopTQRRojo,
            'tgtTQRRojo'=>$tgtTQRRojo,
            'provMin'=>0,
            'provMax'=>0,
            'provRet'=>0,
            'barras'=>0,
            'min'=>0,
            'max'=>0,
            'tendencia'=>0,
            'trendBars'=>0,
            'swingBars'=>0,
            'swingBarsPrev'=>0,
            'pivot0'=>0,
            'pivot1'=>0,
            'pivot2'=>0,
            'pivot3'=>0,
            'pivot4'=>0,
            'retroceso'=>0,
            'extT1'=>0,
            'extT2'=>0,
            'extT3'=>0,
            'extT4'=>0,
            'volumen'=>0,
            'volumenPorSwing'=>0,
            'setup'=>'',
            't1Hit'=>0,
            't2Hit'=>0,
            't3Hit'=>0,
            't4Hit'=>0,
            'entrada'=>'',
            'e' => '',
            'stoploss' => '',
            't1' => '',
            't2' => '',
            't3' => '',
            't4' => '',
            'p' => '',
            'evento' => '',
            'zona' => ''
        ];
    }

    private function armaTablaOperaciones($idSenial, $idTrade, $numeroContratos, $evento, $tipoOperacion, $fechaStr, $desdeHora, $fecha,
                                        $zonaOpen, $zonaHigh, $zonaLow, $zonaClose, $ewo, $bandaSup, $bandaInf,
                                        $precioEntrada, $stopLoss, $target, $swingBars, $contraSwingBars,
                                        $rv, $retroceso, $riesgoPuntos, $riesgoTicks, $riesgoPesos,
                                        $retornoPuntos, $retornoTicks, $retornoPesos, $rrr, $precioCierre,
                                        $plPuntos, $plTicks, $plPesos, $mpc, $mpf, $eficienciaEntrada,
                                        $eficienciaSalida)
    {
        $this->operaciones[] = [
            'idSenial' => $idSenial,
            'idTrade' => $idTrade,
            'numeroContratos' => $numeroContratos,
            'evento' => $evento,
            'tipoOperacion' => $tipoOperacion,
            'fechastr' => $fechaStr,
            'desdeHora' => $desdeHora,
            'fecha' => $fecha,
            'zonaOpen' => $zonaOpen,
            'zonaHigh' => $zonaHigh,
            'zonaLow' => $zonaLow,
            'zonaClose' => $zonaClose,
            'ewo' => $ewo,
            'bandaSup' => $bandaSup,
            'bandaInf' => $bandaInf,
            'precioEntrada' => $precioEntrada,
            'stopLoss' => $stopLoss,
            'target' => $target,
            'swingBars' => $swingBars,
            'contraSwingBars' => $contraSwingBars,
            'rv' => $rv,
            'retroceso' => $retroceso,
            'riesgoPuntos' => $riesgoPuntos,
            'riesgoTicks' => $riesgoTicks,
            'riesgoPesos' => $riesgoPesos,
            'retornoPuntos' => $retornoPuntos,
            'retornoTicks' => $retornoTicks,
            'retornoPesos' => $retornoPesos,
            'rrr' => $rrr,
            'precioCierre' => $precioCierre,
            'plPuntos' => $plPuntos,
            'plTicks' => $plTicks,
            'plPesos' => $plPesos,
            'mpc' => $mpc,
            'mpf' => $mpf,
            'eficienciaEntrada' => $eficienciaEntrada,
            'eficienciaSalida' => $eficienciaSalida
        ];
    }

	public function parametros($desdefecha, $hastafecha, $desdehora, $hastahora, $especie, $calculobase, 
                                $mmcorta, $mmlarga, $compresion, $largovma, $largocci, $largoxtl,
							    $umbralxtl, $calculobase_enum, $swingSize)
	{
        $this->desdeFecha = $desdefecha;
		$this->hastaFecha = $hastafecha;
		$this->desdeHora = $desdehora;
		$this->hastaHora = $hastahora;
        $this->especie = $especie;
		$this->compresion = $compresion;
        $this->calculoBase = $calculobase;
        $this->calculoBase_enum = $calculobase_enum;
        $this->mmCorta = $mmcorta;
        $this->mmLarga = $mmlarga;
        $this->largoVMA = $largovma;
        $this->largoCCI = $largocci;
        $this->largoXTL = $largoxtl;
        $this->umbralXTL = $umbralxtl;
        $this->swingSize = $swingSize;

        switch($compresion)
        {
        case 1:
            $this->compresiontxt = "1 minuto";
            $this->factorCompresion = 1;
            break;
        case 2:
            $this->compresiontxt = "5 minutos";
            $this->factorCompresion = 5;
            break;
        case 3:
            $this->compresiontxt = "15 minutos";
            $this->factorCompresion = 15;
            break;
        case 4:
            $this->compresiontxt = "1 hora";
            $this->factorCompresion = 60;
            break;
        case 5:
            $this->compresiontxt = "1 día";
            $this->factorCompresion = 3600;
            break;
        }

		return $this;
	}

    // Procesa datos para generar ordenes on-line

	public function generaDatosOrdenes($data, $especie, $calculobase, 
                        $mmcorta, $mmlarga, $compresion, $largovma, $largocci, 
                        $largoxtl, $umbralxtl, $swingSize, $filtroSetup, $factorCompresion)
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
        $this->factorCompresion = $factorCompresion;

        $this->k2 = 2 / ($this->mmCorta + $this->mmLarga);
        $this->k1 = 1 - $this->k2;
        $this->flBatch = true;
        // Saltea fechas repetidas
        if ($this->acumFechaLectura != "01-01-2001")
        {
            if ($data->fechalectura == $this->acumFechaLectura)
                return;
        }
        $this->acumFechaLectura = $data->fechalectura;
        $auxFecha = Carbon::parse($data->fechalectura);
        $this->acumMinutoLectura = $auxFecha->minute;

        // Verifica arrancar en divisor del factor de compresion
        if ($this->factorCompresion > 1 && !$this->acumFlEmpezoRango)
        {
            if ($this->acumMinutoLectura % $this->factorCompresion == 0)
                $this->acumFlEmpezoRango = true;
        }
        if ($this->acumFlEmpezoRango)
        {
            // Corte Si es por dia
            $flCorte = false;
            if ($this->factorCompresion == 3600)
            {
                $horaLect = date('H:i', ceil($data->fecha/1000));
                // Corta el dia a las 17:59
                if ($horaLect >= '17:59' && $horaLect < '19:00')
                    $flCorte = true;
            }
            else // Corte si es por minutos
            {
                if ($this->acumFechaInicioRango == '01-01-2001')
                    $this->acumFechaInicioRango = date('Y-m-d H:i', ceil($data->fecha/1000));
                    
                $difMinutos = \Carbon\Carbon::parse($this->acumFechaInicioRango)->diffInMinutes($data->fechalectura);
                if ($difMinutos >= $this->factorCompresion ||
                    ($this->acumMinutoLectura % $this->factorCompresion == 0 && $this->acumFecha != '01-01-2001'))
                    $flCorte = true;
            }
            if ($flCorte)
            {
                switch($this->calculoBase)
                {
                    case 1: // HL2
                        $base = ($this->acumHigh + $this->acumLow) / 2;
                        break;
                    case 2: // HLC3
                        $base = ($this->acumHigh + $this->acumLow + $this->acumClose) / 3;
                        break;
                    case 3: // OHLC4
                        $base = ($this->acumOpen + $this->acumHigh + $this->acumLow + $this->acumClose) / 4;
                        break;
                }

				// Carga tabla de indicadores en memoria
				$this->cargaIndicadores($item);

                $item++;
                $ewo = $bandaSup = $bandaInf = 0;
                $smac = $smal = 0;
                $w4Up1 = $w4Up2 = $w4Dw1 = $w4Dw2 = 0;   
                $rfLim = $rfeExt = $rfeInt = $rfiExt = $rfiInt = $pp1 = $poc = $pp2 = 0;
                $sfiInt = $sfiExt = $sfeInt = $sfeExt = $sfLim = $base = $smac = $smal = $tmp1 = $tmp2 = 0;
                $d1 = $d2 = 0;
                $condicional = $d3 = $k = $VMA = $precioTipico = $SMACCI = $auxCCI = $blanco1 = $blanco2 = 0;
                $CCI = $SMAXTL = $auxXTL = $CCIXTL = $estado = $rango = $TQRVerde = $stopTQRVerde = 0;
                $tgtTQRVerde = $TQRRojo = $stopTQRRojo = $tgtTQRRojo = 0;

                $this->calculaEWO($item, $base, $smac, $smal, $ewo, $bandaSup, $bandaInf,
                    $w4Up1, $w4Up2, $w4Dw1, $w4Dw2);

                // Calcula pivot de fibonacci
                $this->calculaFibonacci($this->acumFechaInicioRango, $rfLim, $rfeExt, $rfeInt, $rfiExt, $rfiInt, $pp1,
                    $poc, $pp2, $sfiInt, $sfiExt, $sfeInt, $sfeExt, $sfLim, $base);

                // Calcula CCI
                $this->CalculaCCI($item, $this->acumHigh, $this->acumLow, $this->acumClose, $precioTipico, 
                    $SMACCI, $auxCCI, $blanco1, $blanco2, $CCI);

                    //dd($this->acumFechaLectura." ".$this->acumFecha);
                // Arma tabla 
                $this->armaTabla($this->acumFechaLectura, $this->acumFecha, $this->acumHoraInicio, 
                                $this->acumOpen, $this->acumClose, $this->acumLow, $this->acumHigh, 
                                $this->acumTotVolume, $ewo,
                                $bandaSup, $bandaInf, $w4Up1, $w4Up2, $w4Dw1, $w4Dw2,
                                $rfLim, $rfeExt, $rfeInt, $rfiExt, $rfiInt, $pp1, $poc, $pp2,
                                $sfiInt, $sfiExt, $sfeInt, $sfeExt, $sfLim, $base, $smac, $smal, $tmp1, $tmp2, 
                                $d1, $d2,
                                $condicional, $d3, $k, $VMA, $precioTipico, $SMACCI, $auxCCI, $blanco1, $blanco2,
                                $CCI, $SMAXTL, $auxXTL, $CCIXTL, $estado, $rango, $TQRVerde, $stopTQRVerde, 
                                $tgtTQRVerde, $TQRRojo, $stopTQRRojo, $tgtTQRRojo);
                                                        
                if ($item > $this->swingSize*2)
                {
                    $this->calculaPivot();
                    // Calcula volumen por swing y Tgt hit
                    $this->calculaSwingTgt();       
                }    
                // Arma tabla 
                for ($i = 0; $i < count($this->datas); $i++)
                {
                    $this->grabaIndicadores($this->datas[$i]['fechastr'], $this->datas[$i]['horainicio'], 
                                        $this->especie, 
                                        $this->datas[$i]['fecha'], $this->datas[$i]['open'], $this->datas[$i]['close'], 
                                        $this->datas[$i]['low'], $this->datas[$i]['high'], $this->datas[$i]['volume'],
                                        $this->datas[$i]['ewo'], $this->datas[$i]['bandaSup'], $this->datas[$i]['bandaInf'], 
                                        $this->datas[$i]['w4Up1'], $this->datas[$i]['w4Up2'], $this->datas[$i]['w4Dw1'], $this->datas[$i]['w4Dw2'],
                                        $this->datas[$i]['provMin'], $this->datas[$i]['provMax'],
                                        $this->datas[$i]['provRet'], $this->datas[$i]['max'], $this->datas[$i]['min'], $this->datas[$i]['swingBarsPrev'], 
                                        $this->datas[$i]['barras'],
                                        $this->datas[$i]['extT1'], $this->datas[$i]['extT2'], $this->datas[$i]['extT3'], $this->datas[$i]['extT4'], 
                                        $this->datas[$i]['setup'], $this->datas[$i]['t1Hit'], $this->datas[$i]['t2Hit'], $this->datas[$i]['t3Hit'], 
                                        $this->datas[$i]['t4Hit'],
                                        $this->datas[$i]['rfeInt'], $this->datas[$i]['rfeExt'], $this->datas[$i]['rfiInt'], $this->datas[$i]['rfiExt'], 
                                        $this->datas[$i]['pp1'], $this->datas[$i]['pp2'], $this->datas[$i]['sfiExt'], $this->datas[$i]['sfiInt'], 
                                        $this->datas[$i]['sfeExt'], $this->datas[$i]['sfeInt'], 
                                        $this->datas[$i]['swingBars'], $this->datas[$i]['precioTipico'], $this->datas[$i]['p'], $this->datas[$i]['evento'], 
                                        $this->datas[$i]['t1'], $this->datas[$i]['t2'], $this->datas[$i]['t3'], $this->datas[$i]['t4'],
                                        $this->datas[$i]['stoploss']);
                }
                $this->acumCantLectura = 0;
                $this->acumLow = $this->acumHigh = $this->acumTotVolume = $this->acumOpen = $this->acumClose = 0;
            }

            $this->acumFecha = $data->fecha;
            $this->acumTotVolume += $data->volume;
            $this->acumCantLectura++;

            // Si es primer lectura del rango inicia variables
            if ($this->acumCantLectura == 1)
            {
                $this->acumFechaInicioRango = $this->acumFechaLectura;
                $this->acumHoraInicio = date('H:i:s', ceil($data->fecha/1000));
                $this->acumOpen = $data->open;
                $this->acumLow = $data->low;
                $this->acumHigh = $data->high;
            }
            else
            {
                if ($data->low < $this->acumLow)
                    $this->acumLow = $data->low;
                if ($data->high > $this->acumHigh)
                    $this->acumHigh = $data->high;
            }  
            $this->acumClose = $data->close;
        }
    }

    private function grabaIndicadores($fecha, $hora, $especie, $fechastr, $open, $close, $low, $high, $volume,
                                        $ewo, $bandasup, $bandainf, $w4up1, $w4up2, $w4dw1, $w4dw2, 
                                        $provmin, $provmax,
                                        $provret, $max, $min, $swingbarsprev, $barras,
                                        $extt1, $extt2, $extt3, $extt4, $setup, $t1hit, $t2hit, $t3hit, $t4hit,
                                        $rfeint, $rfeext, $rfiint, $rfiext, $pp1, $pp2, $sfiext, $sfiint, $sfeext, $sfeint, 
                                        $swingbars, $preciotipico, $p, $evento, $t1, $t2, $t3, $t4, $stoploss)
    {
        $data = DB::connection('trade')->table('trade.indicadores')
                    ->where('fecha', $fecha)->where('hora', $hora)->first();
        
        $datas[] = ['fecha' => $fecha, 
                    'hora' => $hora, 
                    'especie' => $especie, 
                    'chartTime' => $fechastr,
                    'open' => $open, 
                    'high' => $high, 
                    'low' => $low, 
                    'close' => $close, 
                    'volume' => $volume,
                    'ewo' => $ewo, 
                    'bandasup' => $bandasup, 
                    'bandainf' => $bandainf, 
                    'w4up1' => $w4up1, 
                    'w4up2' => $w4up2, 
                    'w4dw1' => $w4dw1, 
                    'w4dw2' => $w4dw2,
                    'provmin' => $provmin,
                    'provmax' => $provmax,
                    'provret' => $provret, 
                    'max' => $max, 
                    'min' => $min, 
                    'swingbarsprev' => $swingbarsprev, 
                    'barras' => $barras,
                    'extt1' => $extt1, 
                    'extt2' => $extt2, 
                    'extt3' => $extt3, 
                    'extt4' => $extt4, 
                    'setup' => $setup, 
                    't1hit' => $t1hit, 
                    't2hit' => $t2hit, 
                    't3hit' => $t3hit, 
                    't4hit' => $t4hit,
                    'rfeint' => $rfeint, 
                    'rfeext' => $rfeext, 
                    'rfiint' => $rfiint, 
                    'rfiext' => $rfiext, 
                    'pp1' => $pp1, 
                    'pp2' => $pp2, 
                    'sfiext' => $sfiext, 
                    'sfiint' => $sfiint, 
                    'sfeext' => $sfeext, 
                    'sfeint' => $sfeint, 
                    'swingbars' => $swingbars, 
                    'preciotipico' => $preciotipico, 
                    'p' => $p, 
                    'evento' => $evento, 
                    't1' => $t1,
                    't2' => $t2,
                    't3' => $t3,
                    't4' => $t4,
                    'stoploss' => $stoploss];

        if ($data)
        {
            $id = $data->id;
            $data = DB::connection('trade')->table('trade.indicadores')->where('id', $id)
                                                                        ->update([
                                                                            'fecha' => $fecha, 
                                                                            'hora' => $hora, 
                                                                            'especie' => $especie, 
                                                                            'chartTime' => $fechastr,
                                                                            'open' => $open, 
                                                                            'high' => $high, 
                                                                            'low' => $low, 
                                                                            'close' => $close, 
                                                                            'volume' => $volume,
                                                                            'ewo' => $ewo, 
                                                                            'bandasup' => $bandasup, 
                                                                            'bandainf' => $bandainf, 
                                                                            'w4up1' => $w4up1, 
                                                                            'w4up2' => $w4up2, 
                                                                            'w4dw1' => $w4dw1, 
                                                                            'w4dw2' => $w4dw2,
                                                                            'provmin' => $provmin,
                                                                            'provmax' => $provmax,
                                                                            'provret' => $provret, 
                                                                            'max' => $max, 
                                                                            'min' => $min, 
                                                                            'swingbarsprev' => $swingbarsprev, 
                                                                            'barras' => $barras,
                                                                            'extt1' => $extt1, 
                                                                            'extt2' => $extt2, 
                                                                            'extt3' => $extt3, 
                                                                            'extt4' => $extt4, 
                                                                            'setup' => $setup, 
                                                                            't1hit' => $t1hit, 
                                                                            't2hit' => $t2hit, 
                                                                            't3hit' => $t3hit, 
                                                                            't4hit' => $t4hit,
                                                                            'rfeint' => $rfeint, 
                                                                            'rfeext' => $rfeext, 
                                                                            'rfiint' => $rfiint, 
                                                                            'rfiext' => $rfiext, 
                                                                            'pp1' => $pp1, 
                                                                            'pp2' => $pp2, 
                                                                            'sfiext' => $sfiext, 
                                                                            'sfiint' => $sfiint, 
                                                                            'sfeext' => $sfeext, 
                                                                            'sfeint' => $sfeint, 
                                                                            'swingbars' => $swingbars, 
                                                                            'preciotipico' => $preciotipico,
                                                                            'p' => $p, 
                                                                            'evento' => $evento, 
                                                                            't1' => $t1,
                                                                            't2' => $t2,
                                                                            't3' => $t3,
                                                                            't4' => $t4,
                                                                            'stoploss' => $stoploss
                                                                        ]);
        }
        else
            $data = DB::connection('trade')->table('trade.indicadores')->insert($datas);
            
        return $data;
    }

	private function cargaIndicadores(&$item)
	{
		$data = DB::connection('trade')->table('trade.indicadores')
				->select('fecha',
                         'especie',
                         'chartTime',
                         'open', 
                         'high', 
                         'low', 
                         'close', 
                         'volume',
                         'ewo', 
                         'bandasup', 
                         'bandainf', 
                         'w4up1', 
                         'w4up2', 
                         'w4dw1', 
                         'w4dw2',
                         'provret', 
                         'provmin',
                         'provmax',
                         'max',
                         'min', 
                         'swingbarsprev', 
                         'barras',
                         'extt1',
                         'extt2', 
                         'extt3', 
                         'extt4', 
                         'setup', 
                         't1hit', 
                         't2hit', 
                         't3hit', 
                         't4hit',
                         'rfeint', 
                         'rfeext', 
                         'rfiint', 
                         'rfiext', 
                         'pp1', 
                         'pp2', 
                         'sfiext', 
                         'sfiint', 
                         'sfeext', 
                         'sfeint', 
                         'swingbars', 
                         'preciotipico',
                         'p', 
                         'evento', 
                         't1',
                         't2',
                         't3',
                         't4',
                         'stoploss')
                ->where('especie', $this->especie)
				->orderBy('fecha', 'desc')
				->take(100)
				->get();

        $this->datas = [];
        $item = 0;
		foreach ($data as $indicador)
		{
            $item++;
            $horaInicio = date("H:i:s", strtotime($indicador->fecha));

            $this->datas[] = ['fechastr'=>$indicador->fecha, 
                            'fecha'=>$indicador->chartTime, 
                            'horainicio'=>$horaInicio,
                            'open'=>$indicador->open, 'close'=>$indicador->close,
                            'low'=>$indicador->low,'high'=>$indicador->high,'volume'=>$indicador->volume,
                            'ewo'=>$indicador->ewo,
                            'bandaSup'=>$indicador->bandasup,
                            'bandaInf'=>$indicador->bandainf,
                            'w4Up1'=>$indicador->w4up1,
                            'w4Up2'=>$indicador->w4up2,
                            'w4Dw1'=>$indicador->w4dw1,
                            'w4Dw2'=>$indicador->w4dw2,
                            'rfeExt'=>$indicador->rfeext,
                            'rfeInt'=>$indicador->rfeint,
                            'rfiExt'=>$indicador->rfiext,
                            'rfiInt'=>$indicador->rfiint,
                            'pp1'=>$indicador->pp1,
                            'pp2'=>$indicador->pp2,
                            'sfiInt'=>$indicador->sfiint,
                            'sfiExt'=>$indicador->sfiext,
                            'sfeInt'=>$indicador->sfeint,
                            'sfeExt'=>$indicador->sfeext,
                            'sfLim'=>0,
                            'base'=>0,
                            'smac'=>0,
                            'smal'=>0,
                            'tmp1'=>0,
                            'tmp2'=>0,
                            'd1'=>0,
                            'd2'=>0,
                            'condicional'=>0,
                            'd3'=>0,
                            'k'=>0,
                            'VMA'=>0,
                            'precioTipico'=>$indicador->preciotipico,
                            'SMACCI'=>0,
                            'auxCCI'=>0,
                            'blanco1'=>0,
                            'blanco2'=>0,
                            'CCI'=>0,
                            'SMAXTL'=>0,
                            'auxXTL'=>0,
                            'CCIXTL'=>0,
                            'estado'=>0,
                            'rango'=>0,
                            'TQRVerde'=>0,
                            'stopTQRVerde'=>0,
                            'tgtTQRVerde'=>0,
                            'TQRRojo'=>0,
                            'stopTQRRojo'=>0,
                            'tgtTQRRojo'=>0,
                            'provMin'=>$indicador->provmin,
                            'provMax'=>$indicador->provmax,
                            'provRet'=>$indicador->provret,
                            'barras'=>$indicador->barras,
                            'min'=>$indicador->min,
                            'max'=>$indicador->max,
                            'tendencia'=>0,
                            'trendBars'=>0,
                            'swingBars'=>$indicador->swingbars,
                            'swingBarsPrev'=>$indicador->swingbarsprev,
                            'pivot0'=>0,
                            'pivot1'=>0,
                            'pivot2'=>0,
                            'pivot3'=>0,
                            'pivot4'=>0,
                            'retroceso'=>0,
                            'extT1'=>$indicador->extt1,
                            'extT2'=>$indicador->extt2,
                            'extT3'=>$indicador->extt3,
                            'extT4'=>$indicador->extt4,
                            'volumen'=>0,
                            'volumenPorSwing'=>0,
                            'setup'=>$indicador->setup,
                            't1Hit'=>$indicador->t1hit,
                            't2Hit'=>$indicador->t2hit,
                            't3Hit'=>$indicador->t3hit,
                            't4Hit'=>$indicador->t4hit,
                            'entrada'=>'',
                            'e' => '',
                            'stoploss' => $indicador->stoploss,
                            't1' => $indicador->t1,
                            't2' => $indicador->t2,
                            't3' => $indicador->t3,
                            't4' => $indicador->t4,
                            'p' => $indicador->p,
                            'evento' => $indicador->evento,
                            'zona' => ''
                        ];
		}
	}

	private function buscaUltimoPivot($offset)
	{
    	$this->tgt = [];
    	$this->tgt[1] = $this->datas[$offset]['t1'];
    	$this->tgt[2] = $this->datas[$offset]['t2'];
    	$this->tgt[3] = $this->datas[$offset]['t3'];
    	$this->tgt[4] = $this->datas[$offset]['t4'];
	}
}
