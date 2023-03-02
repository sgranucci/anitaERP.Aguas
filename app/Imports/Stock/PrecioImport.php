<?php

namespace App\Imports\Stock;

use App\Models\Stock\Precio;
use App\Models\Stock\Listaprecio;
use App\Models\Stock\Articulo;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Auth;
use Carbon\Carbon;

class PrecioImport implements OnEachRow, WithHeadingRow
{
    private $fechavigencia = null, $moneda_id = null, $heading;

    public function  __construct($fechavigencia, $moneda_id, $heading)
    {
        $this->fechavigencia = $fechavigencia;
        $this->moneda_id = $moneda_id;
        $this->heading = $heading;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // Lee el articulo
        $articulo = Articulo::select('id')->where('sku', $row['articulo'])->first();

        $arrayPrecios = [];
        $fechavigencia = Carbon::createFromFormat('d-m-Y', $this->fechavigencia);

        if ($articulo)
        {
            // Verifica que lista de precio tiene que cambiar 
            foreach ($this->heading as $lineaEncabezado)
            {
                foreach ($lineaEncabezado[0] as $nombreColumna)
                {
                    $nombreInicial = substr($nombreColumna, 0, 2);

                    if ($nombreInicial == 'L_' || $nombreInicial == 'l_')
                    {
                        $codigoLista = str_replace($nombreInicial, '', $nombreColumna);

                        // Busca la lista de precios
                        $listaprecio = Listaprecio::select('id')->where('codigo',$codigoLista)->first();

                        if ($listaprecio)
                        {
                            // Lee la lista de precios
                            $precio = Precio::where('articulo_id', $articulo->id)
                                            ->where('listaprecio_id', $listaprecio->id)
                                            ->whereDate('fechavigencia', $fechavigencia)->first();

                            if (!$precio && $row[$nombreColumna] != 0)
                            {
                                $arrayPrecios[] = [
                                    'articulo_id' => $articulo->id,
                                    'listaprecio_id' => $listaprecio->id,
                                    'fechavigencia' => $fechavigencia,
                                    'moneda_id' => $this->moneda_id,
                                    'precio' => $row[$nombreColumna],
                                    'precioanterior' => 0,
                                    'usuarioultcambio_id' => Auth::id(),
                                ];
                            }
                        }
                    }
                }
            }
            
            // Graba los precios de la fila del excel
            foreach ($arrayPrecios as $precio)
            {
                Precio::create($precio);
            }
                
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
