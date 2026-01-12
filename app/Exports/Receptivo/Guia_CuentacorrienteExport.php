<?php

namespace App\Exports\Receptivo;

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
use App\Repositories\Receptivo\Guia_CuentacorrienteRepositoryInterface;
use Carbon\Carbon;
use App\ApiAnita;

class Guia_CuentacorrienteExport implements FromView, WithColumnFormatting, WithMapping, ShouldAutoSize, WithStyles, WithColumnWidths, WithEvents, WithTitle
{
	use Exportable;
	private $busqueda;
    private $guia_cuentacorrienteRepository;
    private $guia_id;
    private $nombreGuia;

    public function __construct(Guia_CuentacorrienteRepositoryInterface $guia_cuentacorrienteRepository, $guia_id, $nombreGuia)
    {
        $this->guia_cuentacorrienteRepository = $guia_cuentacorrienteRepository;
        $this->guia_id = $guia_id;
        $this->nombreGuia = $nombreGuia;
    }

	public function view(): View
	{
        $cuentacorriente = $this->guia_cuentacorrienteRepository->listarCuentaCorriente($this->busqueda, $this->guia_id, false);

		return view('exports.receptivo.guia_cuentacorriente', ['cuentacorriente' => $cuentacorriente, 'guia_id' => $this->guia_id,
                                                                'nombreguia' => $this->nombreGuia]);
	}

	public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_GENERAL,
            'G' => NumberFormat::FORMAT_GENERAL,
            'H' => NumberFormat::FORMAT_GENERAL,
            'I' => NumberFormat::FORMAT_GENERAL,
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
            'I' => ['font' => ['bold' => true]],
            'J' => ['font' => ['bold' => true]],
            'K' => ['font' => ['bold' => true]],
            'L' => ['font' => ['bold' => true]],
            'M' => ['font' => ['bold' => true]],
        ];
    }

	public function columnWidths(): array
    {
        return [
            'A' => 10,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15
        ];
    }

	public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $event->sheet->getDelegate()->freezePane('A4');

            },
        ];
    }

	public function title(): string
    {
        return 'Reporte de Cuenta Corriente de Guías';
    }

	public function parametros($busqueda)
	{
		$this->busqueda = $busqueda;

		return $this;
	}
}
