<?php
namespace App\Services\Ventas;

use App\Repositories\Ventas\PedidoRepositoryInterface;
use App\Repositories\Ventas\Pedido_CombinacionRepositoryInterface;
use App\Repositories\Ventas\Pedido_Combinacion_TalleRepositoryInterface;
use App\Queries\Ventas\PedidoQueryInterface;
use App\Queries\Ventas\ClienteQueryInterface;
use App\Models\Stock\Articulo;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use App;
use PDF;
use Auth;

class PedidoService 
{
	protected $pedidoRepository;
	protected $pedido_combinacionRepository;
	protected $pedido_combinacion_talleRepository;
	protected $pedidoQuery;
	protected $clienteQuery;

    public function __construct(PedidoRepositoryInterface $pedidorepository,
    							Pedido_CombinacionRepositoryInterface $pedidocombinacionrepository,
    							Pedido_Combinacion_TalleRepositoryInterface $pedidocombinaciontallerepository,
								PedidoQueryInterface $pedidoquery,
								ClienteQueryInterface $clientequery
								)
    {
        $this->pedidoRepository = $pedidorepository;
        $this->pedido_combinacionRepository = $pedidocombinacionrepository;
        $this->pedido_combinacion_talleRepository = $pedidocombinaciontallerepository;
        $this->pedidoQuery = $pedidoquery;
        $this->clienteQuery = $clientequery;
    }

	public function leePedido($id)
	{
        $pedido = $this->pedidoRepository->find($id);

        return $pedido;
	}

	public function leePedidosPendientes($cliente_id)
	{
        $hay_pedidos = $this->pedidoQuery->first();

		if (!$hay_pedidos)
		{
			$this->pedidoRepository->sincronizarConAnita();
			$this->pedido_combinacionRepository->sincronizarConAnita();
			$this->pedido_combinacion_talleRepository->sincronizarConAnita();
		}

		return $this->pedidoQuery->allPendiente($cliente_id);
	}

	public function listaPedido($id)
	{
	  	ini_set('memory_limit', '512M');

		$pdfMerger = PDFMerger::init();

		$data = $this->pedidoQuery->leePedidoporId($id);
		$pedido = $data[0];
		$nombre_pdf = 'pedido-'.$id.'-'.$pedido->clientes->nombre;

		$view =  \View::make('exports.ventas.pedido', compact('pedido'))
			    ->render();
		$path = storage_path('pdf/pedido');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save($path.'/'.$nombre_pdf.'.pdf');
        $pdf->download($nombre_pdf.'.pdf');

		return response()->download($path.'/'.$nombre_pdf.'.pdf');

		// Por ahora queda sin hacer el merge
		//$pdfMerger->addPDF($path.'/'.$nombre_pdf.'.pdf', 'all');

		//$pdfMerger->merge();
		//$pdfMerger->save($path.'/pedido.pdf', "file");

		//return response()->download($path.'/pedido.pdf');
	}

	public function guardaPedido($data, $funcion, $id = null)
	{
		$cliente = $this->clienteQuery->traeClienteporId($data['cliente_id']);

		$data['estado'] = '0';
		$data['tipo'] = 'PED';
		$data['letra'] = $cliente->condicionivas->letra;
		$data['sucursal'] = $data['mventa_id'];
		$data['usuario_id'] = Auth::user()->id;
		$data['descuentointegrado'] = ' ';

       	// Pide ultimo numero de pedido en Anita
		if ($funcion == 'create')
		{
       		$this->pedidoRepository->ultimoCodigoAnita($data['tipo'], $data['letra'], $data['sucursal'], $nro);
       		$data['nro'] = $nro;
	
       		$data['codigo'] = $data['tipo'].'-'.$data['letra'].'-'.
                          str_pad($data['sucursal'], 5, "0", STR_PAD_LEFT).'-'.
                          str_pad($data['nro'], 8, "0", STR_PAD_LEFT);

			// Guarda maestro de pedidos 
       		$pedido = $this->pedidoRepository->create($data);
		}
		else
		{
			$data['nro'] = substr($data['codigo'], 12, 8);

       		$pedido = $this->pedidoRepository->update($data, $id);
		}

		// Guarda items
		if ($pedido)
		{
		  	$data['pedido_id'] = ($funcion == 'update' ? $id : $pedido->id);

			// Borra los registros de movimientos antes de grabar nuevamente
			if ($funcion == 'update')
			{
       			$this->pedido_combinacionRepository->deleteporpedido($data['pedido_id'], $data['tipo'],
					$data['letra'], $data['sucursal'], $data['nro']);
			}

   			$articulos = $data['articulos_id'];
   			$combinaciones = $data['combinaciones_id'];
   			$modulos = $data['modulos_id'];
   			$numeroitems = $data['items'];
   			$cantidades = $data['cantidades'];
   			$precios = $data['precios'];
   			$listaprecios = $data['listasprecios_id'];
   			$incluyeimpuestos = $data['incluyeimpuestos'];
   			$monedas = $data['monedas_id'];
   			$descuentos = $data['descuentos'];
   			$medidas = $data['medidas'];
   			$ot_ids = $data['ot_ids'];
   			$observaciones = $data['observaciones'];

   			for ($i_comb = 0; $i_comb < count($articulos); $i_comb++) 
			{
       			if ($articulos[$i_comb] != '') 
				{
					// Lee el articulo
					$articulo = Articulo::select('categoria_id','subcategoria_id','linea_id')->
								where('id',$articulos[$i_comb])->first();

					$categoria_id = $subcategoria_id = $linea_id = NULL;
					if ($articulo)
					{
						$categoria_id = $articulo->categoria_id;
						$subcategoria_id = $articulo->subcategoria_id;
						$linea_id = $articulo->linea_id;
					}

					if ($funcion == 'create')
						$ot_ids[$i_comb] = -1;

					// Guarda item
       				$pedido_combinacion = $this->pedido_combinacionRepository->create(
							$data,
							$data['pedido_id'],
							$articulos[$i_comb],
							$combinaciones[$i_comb],
							$numeroitems[$i_comb],
							$modulos[$i_comb],
							str_replace(',','',$cantidades[$i_comb]),
							str_replace(',','',$precios[$i_comb]),
							$listaprecios[$i_comb],
							$incluyeimpuestos[$i_comb],
							$monedas[$i_comb],
							$descuentos[$i_comb],
							$categoria_id,
							$subcategoria_id,
							$linea_id,
							$ot_ids[$i_comb],
							$observaciones[$i_comb],
							$medidas[$i_comb],
							$funcion
							);

					// Abre medidas de cada item
					$jtalles = json_decode($medidas[$i_comb]);
					foreach ($jtalles as $value)
					{
						// Guarda apertura de talles
						if ($value->cantidad > 0)
       						$this->pedido_combinacion_talleRepository->create(
																		$pedido_combinacion->id, 
																		$value->talle_id, 
																		$value->cantidad, 
																		$value->precio
																		);
					}
				}
			}
		}
	}

	public function borraPedido($id)
	{
		$fl_borro = false;

		$data = $this->pedidoQuery->leePedidoporId($id);

        if (($pedido = $this->pedidoRepository->delete($id)))
		{
			$tipo = substr($data[0]->codigo, 0, 3);
			$letra = substr($data[0]->codigo, 4, 1);
			$sucursal = substr($data[0]->codigo, 6, 5);
			$nro = substr($data[0]->codigo, 12, 8);

        	$pedido_combinacion = $this->pedido_combinacionRepository->deleteporpedido($id, $tipo, $letra, $sucursal, $nro);

			$fl_borro = true;
		}

		return ($fl_borro);
	}
}
