<?php

namespace App\Exports\Ventas;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Ventas\Vendedor;
use Carbon\Carbon;
use App\ApiAnita;

class PedidoExport implements FromView, WithColumnFormatting, WithMapping, ShouldAutoSize, WithStyles, WithColumnWidths, WithEvents, WithTitle
{
	use Exportable;
	private $desdefecha, $hastafecha;
	protected $dates = ['fecha'];

	public function view(): View
	{
		$fecha = strtotime($this->desdefecha);
		$desde_fecha = date('Ymd', $fecha);
		$fecha = strtotime($this->hastafecha);
		$hasta_fecha = date('Ymd', $fecha);

        $apiAnita = new ApiAnita();

        $data = array( 
           	'acc' => 'list', 
			'tabla' => 'pendmae, climae, pendmov, combinacion, stkmae, linmae, mventa',
           	'campos' => '
				penv_fecha as fecha,
               	penv_tipo as tipo,
               	penv_letra as letra,
               	penv_sucursal as sucursal,
               	penv_nro as numero,
               	penv_cliente as cliente,
               	clim_nombre as nombre,
				penv_articulo as articulo,
				penv_capellada as combinacion,
				comb_desc as desc_combinacion,
				(penv_cantidad) as cantidad,
				penm_estado as estado,
				mvta_desc as marca,
				linm_desc as linea,
				penv_nro_orden as nro_orden,
				penv_medida,
				(select sum(stkv_cantidad) from stkmov where
					stkv_ref_tipo="OT" and
					stkv_letra=penv_letra and
					stkv_ref_sucursal=0 and
					stkv_ref_nro=penv_nro_orden and
					stkv_cli_pro=penv_cliente and
					stkv_articulo=penv_articulo) as cantfact
           	' , 
           	'whereArmado' => " WHERE penv_cliente=clim_cliente AND penv_tipo='PED' and
						penv_fecha>=".$desde_fecha." AND penv_fecha<=".$hasta_fecha." and
						penv_tipo=penm_tipo and penv_letra=penm_letra and penv_sucursal=penm_sucursal and
						penv_nro=penm_nro and
						penv_vendedor=".$this->vendedor." and
						penv_articulo=stkm_articulo and
						stkm_linea=linm_linea and
						stkm_o_compra=mvta_mventa and
						penv_articulo=comb_articulo and
						penv_capellada=comb_combinacion",
           	'orderBy' => " penv_cliente, penv_fecha, penv_tipo, penv_letra, penv_sucursal, penv_nro,
					penv_articulo, penv_capellada, penv_medida"
        );
        $datas = json_decode($apiAnita->apiCall($data));

		if ($this->tipolistado == "ABRE")
			return view('exports.ventas.reportepedido.reportepedidoabre', ['comprobantes' => $datas, 'vendedor' => $this->vendedor, 'desdefecha' => $this->desdefecha, 'hastafecha' => $this->hastafecha, 'nombre_vendedor' => $this->nombre_vendedor]);
		else
			return view('exports.ventas.reportepedido.reportepedidototal', ['comprobantes' => $datas, 'vendedor' => $this->vendedor, 'desdefecha' => $this->desdefecha, 'hastafecha' => $this->hastafecha, 'nombre_vendedor' => $this->nombre_vendedor]);
	}

	public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }

	public function map($row): array
    {
        return [
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2   => ['font' => ['bold' => true,
        						'color' => array('rgb' => '17202A'),
        						'size'  => 12,
        						'name'  => 'Arial'
								],
					],
            3   => ['font' => ['bold' => true,
        						'color' => array('rgb' => '17202A'),
        						'size'  => 12,
        						'name'  => 'Arial'
								],
					'fill' => [
                    			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        						'color' => array('rgb' => '85C1E9'),
					]
					],
            'B' => ['font' => ['bold' => true]],
            'G' => ['font' => ['bold' => true]],
            'H' => ['font' => ['bold' => true]],
            'J' => ['font' => ['bold' => true]],
            'M' => ['font' => ['bold' => true]],
        ];
    }

	public function columnWidths(): array
    {
        return [
            'A' => 10,
            'C' => 15,
            'D' => 10,
            'E' => 15,
        ];
    }

	public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $event->sheet->getDelegate()->freezePane('A3');

            },
        ];
    }

	public function title(): string
    {
        return 'Reporte de Pedidos';
    }

	public function rangoFecha($desdefecha, $hastafecha)
	{
		$this->desdefecha = $desdefecha;
		$this->hastafecha = $hastafecha;

		return $this;
	}

	public function asignaVendedor($vendedor)
	{
		$this->vendedor = $vendedor;

        $data = Vendedor::findOrFail($vendedor);

		if ($data)
			$this->nombre_vendedor = $data->nombre;
		else
			$this->nombre_vendedor = '';

		return $this;
	}

	public function asignaTipoListado($tipolistado)
	{
		$this->tipolistado = $tipolistado;

		return $this;
	}
}
